<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model ResetPasswordRequestForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kekaadrenalin\recaptcha3\ReCaptchaWidget;
use app\assets\UserAsset;

UserAsset::register($this);


$this->title = Yii::t('user', 'Elfelejtett jelszó');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container my-5">
    <section class="login-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <?php $form = ActiveForm::begin(['class' => 'md-float-material form-material']); ?>
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="text-center heading mb-4"><?= Yii::t('user', 'Add meg a regisztrációkor használt e-mail címed') ?></h3>
                                    </div>
                                </div>
                                <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('email')])->label(false) ?>
                                <?= $form->field($model, 'captcha')->widget(ReCaptchaWidget::class) ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Html::submitbutton(Yii::t('user', 'Elküld'), ['class' => 'btn btn-primary btn-primary rounded-xl text-center']) ?>
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
