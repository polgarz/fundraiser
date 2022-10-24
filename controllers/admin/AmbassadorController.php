<?php

namespace app\controllers\admin;

use Yii;
use app\models\campaign\Campaign;
use app\models\campaign\CampaignAmbassador;
use app\models\user\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AmbassadorController implements the CRUD actions for CampaignAmbassador model.
 */
class AmbassadorController extends Controller
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
                        'roles' => ['admin', 'fundraiser'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-image' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex($id)
    {
        $model = Campaign::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Nincs ilyen kampány');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getAmbassadors()->joinWith('user'),
            'pagination' => false,
            'sort' => [
                'attributes' => [
                    'name',
                    'goal',
                    'updated_at',
                    'created_at',
                ]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            ]);
    }

    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        $model->approved = 1;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Kampány aktiválva');
        } else {
            Yii::$app->session->setFlash('danger', 'Nem sikerült aktiválni a kampányt');
        }

        return $this->redirect(['index', 'id' => $model->campaign_id]);
    }

    public function actionExport($id)
    {
        $model = Campaign::findOne($id);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getAmbassadors()->joinWith('user'),
            'pagination' => false,
        ]);

        $output = fopen('php://memory', 'w');
        $user = new User;

        foreach($dataProvider->models as $index => $ambassador) {
            if ($index === 0) {
                fputcsv($output, [
                    $ambassador->getAttributeLabel('name'),
                    $ambassador->getAttributeLabel('goal'),
                    $ambassador->getAttributeLabel('collected'),
                    $ambassador->getAttributeLabel('created_at'),
                    $ambassador->getAttributeLabel('approved'),
                    $user->getAttributeLabel('email'),
                    $user->getAttributeLabel('first_name'),
                    $user->getAttributeLabel('last_name'),
                ]);
            }

            fputcsv($output, [
                $ambassador->name,
                Yii::$app->formatter->asCurrency($ambassador->goal),
                Yii::$app->formatter->asCurrency($ambassador->collected),
                Yii::$app->formatter->asDateTime($ambassador->created_at),
                Yii::$app->formatter->asBoolean($ambassador->approved),
                $ambassador->user->email ?? null,
                $ambassador->user->first_name ?? null,
                $ambassador->user->last_name ?? null,
                ]);
        }

        return Yii::$app->response->sendStreamAsFile($output, 'nagykovetek-export-' . date('Y-m-d-H-i') . '.csv');
    }

    /**
     * Displays a single CampaignAmbassador model.
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

    /**
     * Updates an existing CampaignAmbassador model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CampaignAmbassador model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['index', 'id' => $model->campaign_id]);
    }

    public function actionDeleteImage($id)
    {
        Yii::$app->response->format = 'json';

        $model = $this->findModel($id);
        $model->delete_image = 1;
        $model->save();

        return ['success' => 1];
    }

    /**
     * Finds the Campaign model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Campaign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CampaignAmbassador::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}