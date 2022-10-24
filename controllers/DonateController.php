<?php

namespace app\controllers;

use app\models\donation\Donation;
use app\models\donation\DonationForm;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

class DonateController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // disable CSRF validation because of simplepay POST
        if ($action->id == 'ipn') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Donate action
     *
     * @return string
     */
    public function actionDonate($hash)
    {
        $model = Donation::findOne(['hash' => $hash]);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'Ismeretlen hiba törént. Kérjük, próbáld meg újra'));
        }

        // if recurring, login
        if ($model->recurring && Yii::$app->user->isGuest) {
            Yii::$app->user->returnUrl = Url::current();

            return $this->redirect(['user/login']);
        } else if (!Yii::$app->user->isGuest) {
            $model->user_id = Yii::$app->user->id;
            $model->save(false);
        }

        // if it's transfer, then show the transfer details
        if ($model->payment_method == DonationForm::PAYMENT_METHOD_TRANSFER) {
            return $this->render('transfer', ['model' => $model]);
        }

        if ($model->ambassador) {
            $title = $model->ambassador->name;
        } else if ($model->campaign_id) {
            $title = $model->campaign->title;
        } else {
            $title = Yii::t('campaign', 'Általános támogatás');
        }

        $simplePay = Yii::$app->simplePayV2->createSimplePayStart();
        $simplePay->addData('customer', $model->name);
        $simplePay->addData('customerEmail', $model->email);

        if ($model->campaign && $model->campaign->address_required) {
            $simplePay->addData('invoice', [
                'name' => $model->name,
                'zip' => $model->zip,
                'city' => $model->city,
                'address' => $model->street,
                'country' => 'Magyarország',
            ]);
        }

        $simplePay->addData('orderRef', $model->id);
        $simplePay->addItems([
            'ref' => $model->id,
            'title' => $title,
            'description' => $title,
            'amount' => '1',
            'price' => $model->amount,
            'tax' => '0',
        ]);

        if ($model->recurring == 1) {
            $simplePay->addGroupData('recurring', 'times', 24);
            $simplePay->addGroupData('recurring', 'until', date("Y-m-d\TH:i:s+02:00", strtotime('+729 days')));
            $simplePay->addGroupData('recurring', 'maxAmount', $model->amount);
        }

        // auto submit simplepay form
        $simplePay->formDetails['element'] = 'auto';
        $simplePay->runStart();

        $returnData = $simplePay->getReturnData();

        Yii::info([$model->id, 'start'], 'payment');

        if (array_key_exists('errorCodes', $returnData)) {
            $model->status = Donation::STATUS_ERROR;
            $model->save(false);

            Yii::info([$model->id, 'error', $returnData['errorCodes']], 'payment');

            return $this->render('status', [
                'model' => $model,
                'message' => implode(', ', $returnData['errorCodes']),
                'transactionId' => $simplePay->logTransactionId,
            ]);
        }

        // simplepay auto submit form
        $simplePay->getHtmlForm();

        $model->vendor_ref = $returnData['transactionId'];
        $model->save(false);

        // store recurring tokens with dates
        if ($model->recurring == 1 && array_key_exists('tokens', $returnData)) {
            $tokens = $returnData['tokens'];

            foreach($tokens as $i => $token) {
                $recurringModel = new Donation();
                $recurringModel->setAttributes($model->getAttributes());
                $recurringModel->status = Donation::STATUS_READY;
                $recurringModel->token = $token;
                $recurringModel->recurring = 1;
                $recurringModel->token_due_date = date('Y-m-d H:i:s', strtotime('+' . ($i+1) . 'months'));
                $recurringModel->parent_id = $model->id;
                $recurringModel->hash = Yii::$app->security->generateRandomString();

                $recurringModel->save();
            }
        }

        // print simplepay submit form
        echo $simplePay->returnData['form'];
        exit;
    }

    /**
     * If payment success, it process the return data from SimplePay
     *
     * @return string Confirm message to simplepay
     */
    public function actionIpn()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        $json = file_get_contents('php://input');

        $simplePay = Yii::$app->simplePayV2->createSimplePayIpn();

        if ($simplePay->isIpnSignatureCheck($json)) {
            $result = json_decode($json, true);

            Yii::info([$result['orderRef'], 'ipn', $result], 'payment');

            $model = Donation::findOne($result['orderRef']);
            $model->status = Donation::STATUS_FINISHED;

            if ($model->token) {
                $model->token = null;
            }

            $model->save(false);

            if ($model->ambassador && $model->ambassador->user && !$model->parent_id) {
                Yii::$app->mailer->compose([
                        'html' => 'campaign/ambassador-donation-notification'
                    ],[
                        'model' => $model
                    ])->setTo([$model->ambassador->user->email => $model->ambassador->user->fullname])
                    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                    ->setSubject('Új adomány érkezett a kampányodra!')
                    ->send();
            }

            if ($model->recurring && !$model->parent_id) {
                Yii::$app->mailer->compose([
                        'html' => 'donation/recurring-donation-success'
                    ],[
                        'model' => $model
                    ])->setTo([$model->email => $model->name])
                    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                    ->setSubject('Köszönjük a támogatásod!')
                    ->send();
            } elseif ($model->parent_id) {
                Yii::$app->mailer->compose([
                        'html' => 'donation/recurring-donation-monthly'
                    ],[
                        'model' => $model
                    ])->setTo([$model->email => $model->name])
                    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                    ->setSubject('Köszönjük a támogatásod!')
                    ->send();
            } else {
                Yii::$app->mailer->compose([
                        'html' => 'donation/single-donation-success'
                    ],[
                        'model' => $model
                    ])->setTo([$model->email => $model->name])
                    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                    ->setSubject('Köszönjük a támogatásod!')
                    ->send();
            }

            // send response text to simpleypay
            $simplePay->runIpnConfirm();
        }

        exit;
    }

    /**
     * Status page
     *
     * @param string $r payment results, Base64 encoded JSON string
     * @param string $s JSON string salt
     * @return Response|string
     */
    public function actionStatus($r, $s)
    {
        $simplePay = Yii::$app->simplePayV2->createSimplePayBack();
        $result = [];

        if ($simplePay->isBackSignatureCheck($r, $s)) {
            $result = $simplePay->getRawNotification();
        }

        if (count($result) == 0) {
            return $this->goHome();
        }

        $model = Donation::findOne($result['o']);

        Yii::info([$model->id, 'status', $result], 'payment');

        if ($model->status != Donation::STATUS_FINISHED) {
            switch (strtolower($result['e'])) {
                case 'fail':
                    $model->status = Donation::STATUS_ERROR;
                    break;
                case 'timeout':
                    $model->status = Donation::STATUS_TIMEOUT;
                    break;
                case 'cancel':
                    $model->status = Donation::STATUS_CANCELED;
                    break;
                case 'success':
                    $model->status = Donation::STATUS_SUCCESS;
                    break;
                default:
                    $model->status = Donation::STATUS_UNKNOWN;
            }

            $model->save(false);
        }

        return $this->render('status', [
            'message' => '',
            'model' => $model,
            'transactionId' => $simplePay->logTransactionId,
        ]);
    }
}
