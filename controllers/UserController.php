<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\user\LoginForm;
use app\models\user\RegistrationForm;
use app\models\user\ResetPasswordRequestForm;
use app\models\user\ResetPasswordConfirmationForm;
use app\models\user\ResetPasswordForm;
use app\models\user\ProfileForm;
use app\models\user\User;
use app\models\campaign\Campaign;
use app\models\campaign\CampaignAmbassador;
use app\models\donation\Donation;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'profile'],
                'rules' => [
                    [
                        'actions' => ['logout', 'profile', 'cancel-donation'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'cancel-donation' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();

        $user = User::findOne(['email' => $attributes['email']]);

        // login
        if ($user) {
            if (Yii::$app->user->login($user, true)) {

                if (Yii::$app->user->returnUrl) {
                    return $this->redirect(Yii::$app->user->returnUrl);
                }

                return $this->redirect(['site/index']);
            }
        } else {
            // register

            $password = Yii::$app->security->generatePasswordHash(Yii::$app->security->generateRandomString());

            $model = new RegistrationForm([
                'password' => $password,
                'password_repeat' => $password,
                'privacy_policy' => 1,
            ]);

            if ($client instanceof yii\authclient\clients\Google) {
                $model->setAttributes([
                    'email' => $attributes['email'],
                    'first_name' => $attributes['family_name'],
                    'last_name' => $attributes['given_name'],
                ]);
            } else if ($client instanceof yii\authclient\clients\Facebook) {
                $names = explode(' ', $attributes['name']);
                $model->setAttributes([
                    'email' => $attributes['email'],
                    'first_name' => $names[0],
                    'last_name' => $names[1],
                ]);
            }

            if ($model->register()) {
                $user = User::findOne(['email' => $model->email]);
                $user->activation_hash = 1;
                $user->save();

                if (Yii::$app->user->login($user, true)) {
                    if (Yii::$app->user->returnUrl) {
                        return $this->redirect(Yii::$app->user->returnUrl);
                    }

                    return $this->redirect(['site/index']);
                }
            }
        }

        return $this->redirect(['site/index']);
    }

    /**
     * Login standalone page
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(Yii::$app->user->returnUrl);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Registration page
     *
     * @return string
     */
    public function actionRegistration()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegistrationForm();

        if ($model->load(Yii::$app->request->post()) && $model->register()) {

            // send activation mail
            Yii::$app->mailer->compose([
                    'html' => 'user/activate'
                ],[
                    'model' => $model
                ])->setTo([$model->email => $model->first_name . ' ' . $model->last_name])
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setSubject('Csak egy lépésre vagy a regisztrációtól')
                ->send();

            return $this->render('activate', [
                'model' => $model
                ]);
        }

        return $this->render('registration', [
            'model' => $model
            ]);
    }

    /**
     * Activation page, if success logins the user
     *
     * @param string $hash
     * @return string
     */
    public function actionActivate($hash)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $user = User::findOne(['activation_hash' => $hash]);

        if ($user) {
            $user->activation_hash = null;

            if ($user->save()) {
                Yii::$app->user->login($user, 3600*24);
                return $this->redirect(['profile']);
            }
        }

        return $this->render('activate-unsuccessful');
    }

    /**
     * Requests password reset.
     *
     * @return Response
     */
    public function actionPasswordResetRequest()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new ResetPasswordRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->generatePasswordResetCode()) {
            // send mail
            Yii::$app->mailer->compose([
                    'html' => 'user/password-reset'
                ],[
                    'model' => $model
                ])->setTo([$model->email => $model->user->first_name . ' ' . $model->user->last_name])
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setSubject('Elfelejtett jelszó')
                ->send();

            return $this->redirect(['password-reset-confirmation']);
        }

        return $this->render('password-reset-request', [
            'model' => $model,
        ]);
    }

    /**
     * Password reset confirmation page
     *
     * @return string|Response
     */
    public function actionPasswordResetConfirmation()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new ResetPasswordConfirmationForm();

        if ($model->load(Yii::$app->request->post()) && $model->confirm()) {
            return $this->redirect(['password-reset', 'token' => $model->user->auth_key]);
        }

        return $this->render('password-reset-confirmation', [
            'model' => $model,
        ]);
    }

    /**
     * Actual password reset page where user confirms a request with a 6-digit code
     * If it success, logins the user, and redirects to the home page
     *
     * @return string|Response
     */
    public function actionPasswordReset($token)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new ResetPasswordForm($token);

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            Yii::$app->user->login($model->user, 3600*24);
            return $this->goHome();
        }

        return $this->render('password-reset', [
            'model' => $model,
        ]);
    }

    /**
     * Profile page
     *
     * @return string
     */
    public function actionProfile()
    {
        $model = new ProfileForm(Yii::$app->user->identity);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('user', 'Sikeresen módosítottad a profilod'));
                $model->password = null;
                $model->password_repeat = null;
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('user', 'Nem sikerült módosítani a profilod'));
            }
        }

        $campaigns = CampaignAmbassador::find()
            ->joinWith(['campaign'])
            ->where(['user_id' => Yii::$app->user->id, 'status' => Campaign::STATUS_PUBLIC])
            ->all();

        /** @var app\models\user\User */
        $user = Yii::$app->user->identity;

        $donations = $user->getDonations()
            ->where([
                'recurring' => 1,
                'parent_id' => null,
                'status' => Donation::STATUS_FINISHED
                ])
            ->all();

        return $this->render('profile', [
            'model' => $model,
            'campaigns' => $campaigns,
            'donations' => $donations,
        ]);
    }

    public function actionCancelDonation($id)
    {
        $model = Donation::findOne(['user_id' => Yii::$app->user->id, 'id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('campaign', 'Ismeretlen rendszeres adományozás'));
        }

        foreach($model->donations as $donation) {
            if ($donation->token && $donation->status == Donation::STATUS_READY) {
                $donation->delete();
            }
        }

        return $this->redirect(['user/profile']);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
