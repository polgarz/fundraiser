<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

use app\models\donation\Donation;

class DonateController extends Controller
{
    /**
     * Recurring payment
     * Recommended cron: every 20 minutes (*\/20 * * * * without the \)
     *
     * @return string
     */
    public function actionRecurring()
    {
        $tokens = Donation::find()
            ->joinWith('parent AS parent')
            ->where(['<=', 'donation.token_due_date', new \yii\db\Expression('NOW()')])
            ->andWhere(['is not', 'donation.parent_id', null])
            ->andWhere(['is not', 'donation.token_due_date', null])
            ->andWhere(['donation.status' => Donation::STATUS_READY])
            ->andWhere(['parent.status' => Donation::STATUS_FINISHED])
            ->all();

        if ($tokens) {
            $this->stdout(count($tokens) . ' token bevaltasa...' . PHP_EOL, Console::FG_GREEN);
        } else {
            $this->stdout('Nincs aktiv token' . PHP_EOL, Console::FG_YELLOW);
            return false;
        }

        foreach ($tokens as $token) {
            $simplePay = Yii::$app->simplePayV2->createSimplePayDoRecurring();
            $simplePay->addData('token', $token->token);
            $simplePay->addData('orderRef', $token->id);
            $simplePay->addData('total', $token->amount);
            $simplePay->addData('customer', $token->name);
            $simplePay->addData('customerEmail', $token->email);
            $simplePay->runDorecurring();

            $returnData = $simplePay->getReturnData();

            if (array_key_exists('errorCodes', $returnData)) {
                $errorCodes = $returnData['errorCodes'];
                $token->status = Donation::STATUS_ERROR;
                $token->save(false);

                // inactive the recurring payment if one of these error code appear
                if (array_intersect([2072], $errorCodes) !== []) {
                    $parent = Donation::findOne($token->parent_id);
                    $parent->status = Donation::STATUS_EXPIRED;
                    $parent->save();
                }

                Yii::error('Hiba! Kod(ok): ' . join(', ', $errorCodes), 'payment');
                $this->stdout('Hiba! Kod(ok): ' . join(', ', $errorCodes) . PHP_EOL);
                continue;
            }

            $token->created_at = date('Y-m-d H:i:s');
            $token->status = Donation::STATUS_FINISHED;
            $token->vendor_ref = $returnData['transactionId'];
            $token->save(false);

            $this->stdout('Sikeres token bevaltas, tranzakcio azonosito: ' . $token->id . ', szulo tranzakcio: ' . $token->parent->id . PHP_EOL, Console::FG_GREEN);
        }
    }
}