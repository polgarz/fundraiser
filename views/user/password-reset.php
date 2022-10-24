<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\UserAsset;

$this->title = Yii::t('user', 'Új jelszó megadása');
$this->params['breadcrumbs'][] = $this->title;

UserAsset::register($this);
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
                                        <h3 class="text-center heading mb-4"><?= Yii::t('user', 'Másold az alábbi mezőbe az e-mailben kapott ellenőrzőkódot') ?></h3>
                                    </div>
                                </div>
                                <?= $form->field($model, 'password')->passwordInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('password')])->label(false) ?>
                                <?= $form->field($model, 'password_repeat')->passwordInput(['placeholder' => $model->getAttributeLabel('password_repeat')])->label(false) ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Html::submitbutton(Yii::t('app', 'Mentés'), ['class' => 'btn btn-primary btn-primary rounded-xl text-center']) ?>
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
