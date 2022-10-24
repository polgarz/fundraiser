<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\user\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\UserAsset;
use yii\authclient\widgets\AuthChoice;

UserAsset::register($this);

$this->title = Yii::t('user', 'Bejelentkezés');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container my-5">
    <section class="login-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <?php $form = ActiveForm::begin(['id' => 'login-form', 'class' => 'md-float-material form-material']); ?>
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="text-center heading mb-4"><?= Yii::t('user', 'A folytatáshoz kérjük jelentkezz be') ?></h3>
                                    </div>
                                </div>
                                <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('email')])->label(false) ?>
                                <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Html::submitbutton(Yii::t('user', 'Bejelentkezés'), ['class' => 'btn btn-primary btn-primary rounded-xl text-center']) ?>
                                    </div>
                                </div>
                                <div class="or-container">
                                    <div class="line-separator"></div>
                                    <div class="or-label"><?= Yii::t('app', 'vagy') ?></div>
                                    <div class="line-separator"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php $authAuthChoice = AuthChoice::begin(['baseAuthUrl' => ['user/auth']]) ?>
                                            <?php foreach ($authAuthChoice->getClients() as $client): ?>
                                                <a class="btn btn-social btn-white btn-block btn-outline" href="<?= $authAuthChoice->createClientUrl($client) ?>">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <div class="mr-2"><span class="auth-icon <?= $client->name ?>"></span></div>
                                                        <div><?= Yii::t('user', '{social} bejelentkezés', ['social' => ucfirst($client->name)]) ?></div>
                                                    </div>
                                                </a>
                                            <?php endforeach ?>
                                        <?php AuthChoice::end() ?>
                                    </div>
                                </div> <br>
                                <p class="text-inverse text-center">
                                    <?= Html::a(Yii::t('user', 'Regisztráció'), ['user/registration']) ?> &bullet;
                                    <?= Html::a(Yii::t('user', 'Elfelejtett jelszó'), ['user/password-reset-request']) ?>
                                </p>
                            </div>
                        </div>
                    <?php ActiveForm::end() ?>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </section>
</div>