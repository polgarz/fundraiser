<?php

namespace app\controllers\admin;

use Yii;
use app\models\campaign\Campaign;
use app\models\campaign\CampaignAmbassador;
use app\models\tag\Tag;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\web\UploadedFile;
use yii\imagine\Image;

/**
 * CampaignController implements the CRUD actions for Campaign model.
 */
class CampaignController extends Controller
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
                    'delete-og-image' => ['POST'],
                    'delete-cover-image' => ['POST'],
                    'delete-highlight-image' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // disable CSRF validation because of tinymce POST
        if ($action->id == 'upload-image') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all Campaign models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Campaign::find()->orderBy('created_at'),
            'sort' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Campaign model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Campaign([
            'recurring_available' => 1,
            'custom_donation_available' => 1,
            'default_donation_type' => Campaign::DONATION_TYPE_RECURRING,
            ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (Yii::$app->request->post('preview')) {

                $preview = uniqid();

                Yii::$app->session->set($preview, $model->getAttributes());

                return $this->redirect(['/campaign/details', 'slug' => $model->slug, 'preview' => $preview]);
            } else {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'A kampány létrehozva');

                    return $this->redirect(['update', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('error', 'Nem sikerült menteni az adatokat');
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'tagList' => Tag::find()->select('tag')->asArray()->column(),
        ]);
    }

    /**
     * Updates an existing Campaign model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (Yii::$app->request->post('preview')) {

                $preview = uniqid();

                Yii::$app->session->set($preview, $model->getAttributes());

                return $this->redirect(['/campaign/details', 'slug' => $model->slug, 'preview' => $preview]);
            } else {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Sikeres mentés');

                    return $this->redirect(['update', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('error', 'Nem sikerült menteni az adatokat');
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'tagList' => Tag::find()->select('tag')->asArray()->column(),
        ]);
    }

    /**
     * Deletes an existing Campaign model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            Yii::$app->session->setFlash('success', 'Sikeres törlés');
        } else {
            Yii::$app->session->setFlash('error', 'Nem sikerült törölni a kampányt');
        }

        return $this->redirect(['index']);
    }

    /**
     * Uploads an image
     *
     * @return void
     */
    public function actionUploadImage()
    {
        Yii::$app->response->format = 'json';

        $path = Yii::getAlias('@webroot/uploads/images/media/');

        if (!is_dir($path)) {
            \yii\helpers\FileHelper::createDirectory($path);
        }

        $file = UploadedFile::getInstanceByName('file');

        if ($file instanceof UploadedFile) {
            $filename = Inflector::slug(pathinfo($file->name, PATHINFO_FILENAME) . '_' . uniqid());
            $extension = pathinfo($file->name, PATHINFO_EXTENSION);

            $file->saveAs($path . $filename . '.' . $extension);

            try {
                // resize at max 1110px, because bigger is unnecessary
                Image::resize($path . $filename . '.' . $extension, 1110, null)
                    ->save($path . $filename . '.' . $extension)
                    ->save($path . $filename . '.webp');
            } catch(\Exception $e) {
                Yii::error($e->getMessage());
            }

            return ['location' => Yii::getAlias('@web/uploads/images/media/' . $filename . '.' . $extension)];
        }

        return ['location' => ''];
    }

    public function actionDeleteOgImage($id)
    {
        Yii::$app->response->format = 'json';

        $model = $this->findModel($id);
        $model->delete_og_image = 1;
        $model->save();

        return ['success' => 1];
    }

    public function actionDeleteCoverImage($id)
    {
        Yii::$app->response->format = 'json';

        $model = $this->findModel($id);
        $model->delete_cover_image = 1;
        $model->save();

        return ['success' => 1];
    }

    public function actionDeleteHighlightImage($id)
    {
        Yii::$app->response->format = 'json';

        $model = $this->findModel($id);
        $model->delete_highlight_image = 1;
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
        if (($model = Campaign::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
