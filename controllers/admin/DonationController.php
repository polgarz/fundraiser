<?php

namespace app\controllers\admin;

use Yii;
use app\models\campaign\Campaign;
use app\models\campaign\CampaignAmbassador;
use app\models\donation\Donation;
use app\models\donation\DonationForm;
use app\models\donation\DonationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * DonationController implements the CRUD actions for Donation model.
 */
class DonationController extends Controller
{
    public $layout = 'admin';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'approve' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Donation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DonationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $campaignList = Campaign::find()->orderBy('created_at DESC')->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'campaignList' => $campaignList,
        ]);
    }

    /**
     * Displays a single Donation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        $model->status = Donation::STATUS_FINISHED;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Adomány elfogadva');
        } else {
            Yii::$app->session->setFlash('danger', 'Nem sikerült elfogadni az adományt');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionExport()
    {
        $searchModel = new DonationSearch();
        $model = new Donation();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $output = fopen('php://memory', 'w');

        fputcsv($output, [
            $model->getAttributeLabel('id'),
            $model->getAttributeLabel('payment_method'),
            $model->getAttributeLabel('amount'),
            $model->getAttributeLabel('name'),
            $model->getAttributeLabel('anonymous'),
            $model->getAttributeLabel('email'),
            $model->getAttributeLabel('newsletter'),
            $model->getAttributeLabel('campaign_id'),
            $model->getAttributeLabel('campaign_ambassador_id'),
            $model->getAttributeLabel('status'),
            $model->getAttributeLabel('zip'),
            $model->getAttributeLabel('city'),
            $model->getAttributeLabel('street'),
            $model->getAttributeLabel('note'),
            $model->getAttributeLabel('vendor_ref'),
            $model->getAttributeLabel('recurring'),
            $model->getAttributeLabel('created_at'),
        ]);

        /** @var yii\db\Query */
        $query = $dataProvider->query;
        foreach($query->each() as $donation) {

            if ($donation->recurring && !$donation->parent_id) {
                $recurring = 'Igen (' . ($donation->hasActiveTokens ? 'aktív' : 'inaktív') . ',  ' . $donation->recurringSequenceNumber . '. alkalom)';
            } elseif ($donation->recurring) {
                $recurring = 'Igen (' . ($donation->parent->hasActiveTokens ? 'aktív' : 'inaktív') . ',  ' . $donation->recurringSequenceNumber . '. alkalom)';
            } else {
                $recurring = 'Nem';
            }


            fputcsv($output, [
                $donation->id,
                $donation->paymentMethodList[$donation->payment_method],
                $donation->amount,
                $donation->name,
                Yii::$app->formatter->asBoolean($donation->anonymous),
                $donation->email,
                Yii::$app->formatter->asBoolean($donation->newsletter),
                $donation->campaign->title ?? null,
                $donation->ambassador->name ?? null,
                $donation->statusList[$donation->status],
                $donation->zip,
                $donation->city,
                $donation->street,
                $donation->note,
                $donation->vendor_ref,
                $recurring,
                $donation->created_at,
                ]);
        }

        return Yii::$app->response->sendStreamAsFile($output, 'adomanyok-export-' . date('Y-m-d-H-i') . '.csv');
    }

    /**
     * Updates an existing Donation model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $campaignList = Campaign::find()
            ->select(['title', 'id'])
            ->indexBy('id')
            ->orderBy('id DESC')
            ->column();

        $ambassadorList = CampaignAmbassador::find()
            ->orderBy('name')
            ->all();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Sikeres mentés');

                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Nem sikerült menteni az adatokat');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'campaignList' => $campaignList,
            'ambassadorList' => $ambassadorList,
        ]);
    }

    /**
     * Deletes an existing Donation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->payment_method === DonationForm::PAYMENT_METHOD_TRANSFER
            && $model->status === Donation::STATUS_TRANSFER_ADDED && $model->delete()) {
            Yii::$app->session->setFlash('success', 'Sikeres törlés');
        } else {
            Yii::$app->session->setFlash('error', 'Nem sikerült törölni az adományt');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Donation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Donation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Donation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
