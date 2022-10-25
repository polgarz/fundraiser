<?php

namespace app\controllers;

use app\models\campaign\Campaign;
use app\models\donation\DonationForm;
use app\models\user\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['admin'],
                'rules' => [
                    [
                        'actions' => ['admin'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $donationFormModel = new DonationForm();
        $campaigns = Campaign::find()
            ->where(['archive' => 0, 'status' => Campaign::STATUS_PUBLIC, 'highlighted' => 0])
            ->orderBy('created_at DESC')
            ->all();


        if ($donationFormModel->load(Yii::$app->request->post()) && $donationFormModel->save()) {
            return $this->redirect(['donate/donate', 'hash' => $donationFormModel->hash]);
        }

        return $this->render('index', [
            'donationFormModel' => $donationFormModel,
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Redirects to admin.
     *
     * @return string
     */
    public function actionAdmin()
    {
        if (Yii::$app->user->can(User::GROUP_ADMIN)) {
            return $this->redirect(['admin/user/index']);
        } else if (Yii::$app->user->can(User::GROUP_FUNDRAISER)) {
            return $this->redirect(['admin/ambassador/index']);
        }
    }
}
