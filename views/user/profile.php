<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model ProfileForm */
/* @var $donations Donation[] */
/* @var $purchases Purchase[] */
/* @var $courses Course[] */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\UserAsset;
use app\models\user\User;

UserAsset::register($this);

$this->title = Yii::t('user', 'Profil');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container my-5">
    <section class="profile-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-3"><h1><?= $this->title ?></h1></div>
                        <div class="col-9 text-right">
                            <a class="btn btn-primary btn-primary rounded-xl text-center" href="<?= Url::to(['user/logout']) ?>" data-method="post"><?= Yii::t('user', 'Kijelentkezés') ?></a>
                            <?php if (Yii::$app->user->can(User::GROUP_EDITOR) || Yii::$app->user->can(User::GROUP_ADMIN)): ?>
                                <a class="btn btn-secondary btn-primary rounded-xl text-center" href="<?= Url::to(['site/admin']) ?>">Admin</a>
                            <?php endif ?>
                        </div>
                    </div>
                    <?php $form = ActiveForm::begin(['id' => 'profile-form', 'class' => 'md-float-material form-material']); ?>
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="mb-4"><?= Yii::t('user', 'Alapadatok') ?></h3>
                                        <div class="row">
                                            <div class="col-md-6"><?= $form->field($model, 'first_name')->textInput(['placeholder' => $model->getAttributeLabel('first_name')])->label(false) ?></div>
                                            <div class="col-md-6"><?= $form->field($model, 'last_name')->textInput(['placeholder' => $model->getAttributeLabel('last_name')])->label(false) ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12"><?= $form->field($model, 'email')->textInput(['placeholder' => $model->getAttributeLabel('email')])->label(false) ?></div>
                                        </div>

                                        <h5><?= Yii::t('user', 'Jelszó módosítása') ?></h5>
                                        <hr class="mt-2 mb-3" />

                                        <div class="row">
                                            <div class="col-md-6"><?= $form->field($model, 'password')->textInput(['placeholder' => $model->getAttributeLabel('password')])->label(false)->hint(Yii::t('user', 'Hagyd üresen, ha nem akarod módosítani')) ?></div>
                                            <div class="col-md-6"><?= $form->field($model, 'password_repeat')->textInput(['placeholder' => $model->getAttributeLabel('password_repeat')])->label(false) ?></div>
                                        </div>

                                        <?= Html::submitButton(Yii::t('app', 'Mentés'), ['class' => 'btn btn-primary btn-primary rounded-xl text-center']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php ActiveForm::end() ?>

                    <?php if ($donations): ?>
                        <div class="auth-box card mt-5">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="mb-4"><?= Yii::t('user', 'Rendszeres adományaim') ?></h3>

                                        <?php foreach($donations as $donation): ?>
                                            <h5 class="text-secondary mx-2 mt-3"><?= Yii::$app->formatter->asDate($donation->created_at) ?> - <?= Yii::$app->formatter->asCurrency($donation->amount) ?></h5>

                                            <?php if ($donation->hasActiveTokens): ?>
                                                <a href="<?= Url::to(['cancel-donation', 'id' => $donation->id]) ?>" class="btn btn-primary rounded-xl btn-sm" data-method="post" data-confirm="Biztosan leállítod a rendszeres adományozást?">
                                                    <?= Yii::t('campaign/ambassador', 'Adományozás leállítása') ?>
                                                </a>
                                            <?php endif ?>
                                            <hr />

                                            <div class="row mx-2 align-items-center">
                                                <div class="col-sm-2">
                                                    <small><?= Yii::t('campaign', 'Összeg') ?></small>
                                                </div>
                                                <div class="col-sm-3 text-left">
                                                    <small><?= Yii::t('campaign', 'Dátum') ?></small>
                                                </div>
                                                <div class="col-sm-3 text-left">
                                                    <small><?= Yii::t('campaign', 'SimplePay azonosító') ?></small>
                                                </div>
                                                <div class="col-sm-4 text-right">
                                                    <small><?= Yii::t('campaign', 'Státusz') ?></small>
                                                </div>
                                            </div>
                                            <?php foreach($donation->donations as $child): ?>
                                                <div class="row my-2 border mx-2 align-items-center">
                                                    <div class="col-sm-2 py-2">
                                                        <strong><?= Yii::$app->formatter->asCurrency($child->amount) ?></strong>
                                                    </div>
                                                    <div class="col-sm-3 text-left">
                                                        <?= Yii::$app->formatter->asDate($child->token_due_date) ?>
                                                    </div>
                                                    <div class="col-sm-3 text-left">
                                                        <?= $child->vendor_ref ?>
                                                    </div>
                                                    <div class="col-sm-4 text-right">
                                                        <?= $child->statusList[$child->status] ?>
                                                    </div>
                                                </div>
                                            <?php endforeach ?>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>

                    <?php if ($campaigns): ?>
                        <div class="auth-box card mt-5">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="mb-4"><?= Yii::t('user', 'Kampányaim') ?></h3>

                                        <?php foreach($campaigns as $campaign): ?>
                                            <h5 class="text-secondary mx-2 mt-3"><?= $campaign->campaign->title ?></h5>
                                            <div class="row align-items-center border mx-2">
                                                <div class="col-md-6">
                                                    <div class="float-left mt-1 mb-1 text-secondary">
                                                        <small><strong><?= Yii::$app->formatter->asCurrency($campaign->collected) ?></strong> <?= Yii::t('campaign/ambassador', 'összegyűjtve') ?></small>
                                                    </div>
                                                    <div class="float-right mt-1 mb-1 text-secondary">
                                                        <small><strong><?= Yii::$app->formatter->asPercent($campaign->collected / $campaign->goal) ?></strong></small>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: <?= Yii::$app->formatter->asPercent($campaign->collected / $campaign->goal) ?>" aria-valuenow="<?= $campaign->collected / $campaign->goal * 100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="text-muted small float-left mb-1 mt-1">
                                                        <small><i><?= Yii::t('campaign/ambassador', '{goal} cél', ['goal' => Yii::$app->formatter->asCurrency($campaign->goal)]) ?></i></small>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <a href="<?= Url::to(['campaign/ambassador', 'campaign_slug' => $campaign->campaign->slug, 'slug' => $campaign->slug]) ?>" class="btn btn-primary rounded-xl btn-sm">
                                                        <?= Yii::t('campaign/ambassador', 'Kampányom megtekintése') ?>
                                                    </a>
                                                    <?php if (!$campaign->campaign->archive): ?>
                                                        <a href="<?= Url::to(['campaign/update-ambassador-campaign', 'id' => $campaign->id]) ?>" title="Kampány szerkesztése" class="mx-1 btn btn-outline-primary rounded-circle btn-sm">
                                                            <i class="fa fa-pen"></i>
                                                        </a>
                                                    <?php endif ?>
                                                    <a href="<?= Url::to(['campaign/delete-ambassador-campaign', 'id' => $campaign->id]) ?>" title="Kampány törlése" class="mx-1 btn btn-outline-primary rounded-circle btn-sm float-right" data-confirm="<?= Yii::t('campaign/ambassador', 'Biztosan törlöd a kampányod? Ezt a műveletet nem lehet visszavonni!') ?>" data-method="post">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </div>
</div>
