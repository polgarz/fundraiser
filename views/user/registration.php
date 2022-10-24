<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model RegistrationForm */


use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\UserAsset;
use yii\authclient\widgets\AuthChoice;
use yii\helpers\Url;

UserAsset::register($this);

$this->title = Yii::t('user', 'Regisztráció');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container my-5">
    <section class="login-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <?php $form = ActiveForm::begin(['id' => 'registration-form', 'class' => 'md-float-material form-material']); ?>
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="text-center heading mb-4"><?= Yii::t('user', 'Regisztráció') ?></h3>
                                    </div>
                                </div>
                                <?= $form->field($model, 'first_name')->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('first_name')])->label(false) ?>
                                <?= $form->field($model, 'last_name')->textInput(['placeholder' => $model->getAttributeLabel('last_name')])->label(false) ?>
                                <?= $form->field($model, 'email')->textInput(['placeholder' => $model->getAttributeLabel('email')])->label(false) ?>
                                <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>
                                <?= $form->field($model, 'password_repeat')->passwordInput(['placeholder' => $model->getAttributeLabel('password_repeat')])->label(false) ?>
                                <?= $form->field($model, 'privacy_policy')->checkbox()->label(Yii::t('user', 'Elolvastam, és elfogadom az <a href="{url}">adatvédelmi szabályzatot</a>', ['url' => Url::to(['static/privacy-policy'])])) ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Html::submitbutton(Yii::t('user', 'Regisztráció'), ['class' => 'btn btn-primary btn-md btn-block text-center m-b-20']) ?>
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
                                </div>
                            </div>
                        </div>
                    <?php ActiveForm::end() ?>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </section>
</div>



