<?php

namespace app\controllers;

use Yii;
use app\models\campaign\Campaign;
use app\models\campaign\ApplyForm;
use app\models\campaign\CampaignAmbassador;
use app\models\campaign\UpdateAmbassadorCampaignForm;
use app\models\donation\Donation;
use app\models\donation\DonationForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

class CampaignController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['apply'],
                'rules' => [
                    [
                        'actions' => [
                            'apply',
                            'update-ambassador-campaign',
                            'delete-ambassador-campaign',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-ambassador-campaign' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Campaign details
     *
     * @param string $slug
     * @param string $preview
     * @return string
     */
    public function actionDetails($slug, $preview = null)
    {
        if (!$preview) {
            $model = Campaign::find()
                ->where(['slug' => $slug, 'status' => Campaign::STATUS_PUBLIC])
                ->one();
        } else {
            $model = new Campaign();
            $previewData = Yii::$app->session->get($preview);

            if (!$previewData) {
                throw new \Exception(Yii::t('campaign', 'Hiba történt az előnézet betöltése közben'));
            }

            $model->setAttributes($previewData);
        }

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('campaign', 'Nincs ilyen kampány'));
        }

        $donationFormModel = new DonationForm([
            'recurring_available' => $model->recurring_available,
            'default_donation_type' => $model->default_donation_type,
            'custom_donation_available' => $model->custom_donation_available,
            'address_required' => $model->address_required,
            'campaign_id' => $model->id,
            'donation_options' => ArrayHelper::map($model->getDonationOptions()->orderBy('order')->all(), 'value', 'name'),
            ]);

        if ($donationFormModel->load(Yii::$app->request->post()) && $donationFormModel->save()) {
            return $this->redirect(['donate/donate', 'hash' => $donationFormModel->hash]);
        }

        return $this->render('details', [
            'model' => $model,
            'donationFormModel' => $donationFormModel,
            'ambassadors' => $model->getAmbassadors()->where(['approved' => 1])->orderBy('created_at DESC')->all(),
            ]);
    }

    /**
     * Ambassador details
     *
     * @param string $campaign_slug
     * @param string $slug
     * @return string
     */
    public function actionAmbassador($campaign_slug, $slug)
    {
        $campaign = Campaign::find()
            ->where(['slug' => $campaign_slug, 'status' => Campaign::STATUS_PUBLIC])
            ->one();

        if (!$campaign) {
            throw new NotFoundHttpException(Yii::t('campaign', 'Nincs ilyen kampány'));
        }

        $model = $campaign->getAmbassadors()->where(['approved' => 1, 'slug' => $slug])->one();

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('campaign', 'Nem található nagykövet ezen a linken'));
        }

        $donationFormModel = new DonationForm([
            'recurring_available' => $campaign->recurring_available,
            'default_donation_type' => $campaign->default_donation_type,
            'custom_donation_available' => $campaign->custom_donation_available,
            'campaign_id' => $campaign->id,
            'donation_options' => ArrayHelper::map($campaign->getDonationOptions()->orderBy('order')->all(), 'value', 'name'),
            'ambassador_id' => $model->id,
            ]);

        if ($donationFormModel->load(Yii::$app->request->post()) && $donationFormModel->save()) {
            return $this->redirect(['donate/donate', 'hash' => $donationFormModel->hash]);
        }

        return $this->render('ambassador', [
            'model' => $model,
            'campaign' => $campaign,
            'donationFormModel' => $donationFormModel,
            ]);
    }

    /**
     * Update ambassador campaign (for users)
     *
     * @param int $id Campaign id
     * @return string
     */
    public function actionUpdateAmbassadorCampaign($id)
    {
        $model = CampaignAmbassador::find()
            ->joinWith(['campaign'])
            ->where([
                'campaign_ambassador.id' => $id,
                'status' => Campaign::STATUS_PUBLIC,
                'archive' => 0,
                'user_id' => Yii::$app->user->id
                ])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('campaign', 'Nincs ilyen kampány'));
        }

        $formModel = new UpdateAmbassadorCampaignForm($model);

        if ($formModel->load(Yii::$app->request->post()) && $formModel->save()) {
            Yii::$app->session->setFlash('success', Yii::t('campaign/ambassador', 'Sikeresen módosítottad a kampányodat'));
        }

        return $this->render('update-ambassador-campaign', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    /**
     * Delete ambassador campaign (for users)
     *
     * @param int $id Campaign id
     * @return string
     */
    public function actionDeleteAmbassadorCampaign($id)
    {
        $model = CampaignAmbassador::find()
            ->joinWith(['campaign'])
            ->where([
                'campaign_ambassador.id' => $id,
                'status' => Campaign::STATUS_PUBLIC,
                'user_id' => Yii::$app->user->id
                ])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('campaign', 'Nincs ilyen kampány'));
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('campaign/ambassador', 'A kampányod sikeresen törölve'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('campaign/ambassador', 'Hiba történt a kampányod törlése során, kérjük, próbáld újra!'));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Apply as ambassador
     *
     * @param string $slug
     * @return string
     */
    public function actionApply($slug)
    {
        $model = Campaign::find()
            ->where(['slug' => $slug, 'status' => Campaign::STATUS_PUBLIC])
            ->one();

        $applyForm = new ApplyForm($model, ['name' => Yii::$app->user->identity->fullname]);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('campaign', 'Nincs ilyen kampány'));
        } else if (!$model->ambassador_can_apply && !$model->archive) {
            throw new NotFoundHttpException(Yii::t('campaign', 'Erre a kampányra nem tudsz jelentkezni'));
        }

        if ($applyForm->load(Yii::$app->request->post()) && $applyForm->save()) {
            return $this->render('apply-success', ['model' => $model]);
        }

        return $this->render('apply', [
            'model' => $model,
            'applyForm' => $applyForm,
        ]);
    }
}
