<?php

/* @var $this yii\web\View */
/* @var $model app\models\campaign\Campaign */

use app\assets\CampaignAsset;
use app\widgets\DonationFormWidget;
use app\models\user\User;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Html::encode($model->name);

CampaignAsset::register($this);

$this->registerMetaTag(['name' => 'og:image', 'content' => Url::to($model->getUploadUrl('image'), true)]);
?>

<div class="mx-auto text-secondary pt-5 px-0 container-fluid">
    <div class="container post-details page">
        <div class="row">
            <div class="col-md-8">
                <h1 class="text-uppercase text-left"><?= Html::encode($model->name) ?></h1>

                <p>
                    <?= implode(' &bull; ', [
                        Html::a($campaign->title, ['campaign/details', 'slug' => $campaign->slug]),
                        (Yii::$app->user->can(User::GROUP_ADMIN) ? Html::a('Szerkesztés', ['admin/ambassador/update', 'id' => $model->id]) : null)
                        ]) ?>
                </p>
                <p><?= nl2br($model->content) ?></p>
                <p class="text-center"><?= Html::img($model->getUploadUrl('image'), ['class' => 'w-50 mx-auto']) ?></p>

                <?php if ($donations = $model->donations): ?>
                    <div class="campaign-donations">
                        <h2 class="m-0 mt-5 p-1 mb-4"><?= Yii::t('campaign', 'Adományozók') ?></h2>

                        <?php foreach($donations as $donation): ?>
                            <div class="row">
                                <div class="col-4 col-md-2 text-right text-primary amount">
                                    <div><?= Yii::$app->formatter->asCurrency($donation->amount) ?></div>
                                    <?php if ($donation->recurring): ?>
                                        <div><small class="font-weight-normal"><?= Yii::t('campaign', 'rendszeres') ?></small></div>
                                    <?php else: ?>

                                    <?php endif ?>
                                </div>
                                <div class="col-8 col-md-10">
                                    <div class="title float-md-left">
                                        <?php if (!$donation->anonymous): ?>
                                            <strong><?= $donation->name ?></strong>
                                        <?php else: ?>
                                            <strong><?= Yii::t('campaign', 'Anonim támogatás') ?></strong>
                                        <?php endif ?>
                                        <span class="date">
                                            <?= Yii::$app->formatter->asRelativeTime($donation->created_at) ?>
                                        </span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="message mt-1"><?= $donation->message ?>&nbsp;</div>
                                    <hr>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="col-md-4">
                <div class="campaign-volunteers-section">
                    <div class="card mt-6 border-bottom-0">
                        <div class="py-5 px-3 text-center">
                            <?php if ($image = $model->getThumbUploadUrl('image', 'list')): ?>
                                <?php $info = pathinfo($image); ?>
                                <picture>
                                    <source srcset="<?= $info['dirname'] . '/' . $info['filename'] . '.webp' ?>" type="image/webp">
                                    <source srcset="<?= $image ?>" type="image/jpeg">
                                    <img src="<?= $image ?>" loading="lazy" alt="<?= $model->name ?>" class="p-0 mx-auto rounded-circle ambassador-profile-img-sm">
                                </picture>
                            <?php endif ?>
                            <h5 class="text-uppercase text-secondary m-0 mt-4 text-center"><?= $model->name ?></h5>
                        </div>
                        <div class="footer">
                            <div class="float-left mt-3 ml-3 mb-2 text-secondary">
                                <strong><?= Yii::$app->formatter->asCurrency($model->collected) ?></strong> <?= Yii::t('campaign/ambassador', 'összegyűjtve') ?>
                            </div>
                            <div class="float-right mt-3 mr-3 mb-2 text-secondary">
                                <strong><?= Yii::$app->formatter->asPercent($model->collected / $model->goal) ?></strong>
                            </div>
                            <div class="clearfix"></div>
                            <div class="progress mx-3">
                            <div class="progress-bar" role="progressbar" style="width: <?= Yii::$app->formatter->asPercent($model->collected / $model->goal) ?>" aria-valuenow="<?= $model->collected / $model->goal * 100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-muted small float-left ml-3 mr-3 mb-3 mt-2">
                                <i><?= Yii::t('campaign/ambassador', '{goal} cél', ['goal' => Yii::$app->formatter->asCurrency($model->goal)]) ?></i>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <?= DonationFormWidget::widget(['model' => $donationFormModel, 'label' => Yii::t('campaign/ambassador', 'Támogasd te is {name} kampányát!', ['name' => $model->name])]) ?>
            </div>
        </div>
    </div>
</div>