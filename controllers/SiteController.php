<?php

namespace app\controllers;

use app\models\user\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

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
        return $this->render('index');
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
