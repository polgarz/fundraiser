<?php

/* @var $this yii\web\View */
/* @var $model app\models\campaign\Campaign */

use app\assets\CampaignAsset;
use app\widgets\DonationFormWidget;
use app\models\user\User;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->title;

CampaignAsset::register($this);

if ($model->tags) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => implode(',', $model->tags)], 'meta-keywords');
}

if ($model->meta_description) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description], 'meta-description');
}

if ($model->og_image) {
    $this->registerMetaTag(['name' => 'og:image', 'content' => Url::to($model->getBehavior('og')->getUploadUrl('og_image'), true)], 'og-image');
}

if ($model->og_description) {
    $this->registerMetaTag(['name' => 'og:description', 'content' => $model->og_description], 'og-description');
}
?>

<div class="mx-auto text-secondary pt-5 px-0 container-fluid">
    <div class="container post-details page">
        <div class="row">
            <div class="col-md-8">
                <h1 class="text-uppercase text-left"><?= Html::encode($model->title) ?></h1>

                <?php if (Yii::$app->user->can(User::GROUP_ADMIN)): ?>
                    <p>
                        <?= Html::a('Szerkesztés', ['admin/campaign/update', 'id' => $model->id]) ?>
                    </p>
                <?php endif ?>

                <?= $model->getRenderedContent() ?>

                <?php if ($model->ambassador_can_apply && !$model->archive): ?>
                    <p class="text-center">
                        <?= Html::a(Yii::t('campaign/apply', 'Én is szeretnék gyűjteni'), ['campaign/apply', 'slug' => $model->slug], ['class' => 'btn btn-primary rounded-xl']) ?>
                    </p>
                <?php endif ?>
            </div>

            <div class="col-md-4">
                <?= DonationFormWidget::widget(['model' => $donationFormModel]) ?>
            </div>
        </div>
    </div>
    <?php if ($ambassadors): ?>
        <section class="bg-gradient campaign-volunteers-section text-center pt-5">
            <div class="center-section mx-auto">
                <h2 class="text-uppercase m-0 p-1"><?= Yii::t('campaign/ambassador', 'Kampányok') ?></h2>
                <div class="row p-0 m-0 mt-8">
                    <?php foreach($ambassadors as $ambassador): ?>
                        <?php $collected = $ambassador->collected; ?>
                        <div class="col-md-4 mb-md-8">
                            <div class="card mb-7 mb-md-0 mx-3 mx-md-0">
                                <div class="py-5 px-3">
                                    <?php if ($image = $ambassador->getThumbUploadUrl('image', 'list')): ?>
                                        <?php $info = pathinfo($image); ?>
                                        <picture>
                                            <source srcset="<?= $info['dirname'] . '/' . $info['filename'] . '.webp' ?>" type="image/webp">
                                            <source srcset="<?= $image ?>" type="image/jpeg">
                                            <img src="<?= $image ?>" loading="lazy" alt="<?= $ambassador->name ?>" class="p-0 mx-auto rounded-circle ambassador-profile-img-sm">
                                        </picture>
                                    <?php endif ?>
                                    <h5 class="text-uppercase text-secondary m-0 mt-4"><?= $ambassador->name ?></h5>
                                    <div class="py-1 text-secondary text-center">
                                        <p class="py-3 m-0"><?= $ambassador->lead ?></p>
                                        <?= Html::a(Yii::t('campaign/ambassador', 'Adományozok'), ['campaign/ambassador', 'campaign_slug' => $model->slug, 'slug' => $ambassador->slug], ['class' => 'btn btn-primary rounded-xl btn-sm']) ?>
                                    </div>
                                </div>
                                <div class="footer">
                                    <div class="float-left mt-3 ml-3 mb-2 text-secondary">
                                        <strong><?= Yii::$app->formatter->asCurrency($collected) ?></strong> <?= Yii::t('campaign/ambassador', 'összegyűjtve') ?>
                                    </div>
                                    <div class="float-right mt-3 mr-3 mb-2 text-secondary">
                                        <strong><?= Yii::$app->formatter->asPercent($collected / $ambassador->goal) ?></strong>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="progress mx-3">
                                    <div class="progress-bar" role="progressbar" style="width: <?= Yii::$app->formatter->asPercent($collected / $ambassador->goal) ?>" aria-valuenow="<?= $collected / $ambassador->goal * 100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="text-muted small float-left ml-3 mr-3 mb-3 mt-2">
                                        <i><?= Yii::t('campaign/ambassador', '{goal} cél', ['goal' => Yii::$app->formatter->asCurrency($ambassador->goal)]) ?></i>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        </section>
    <?php endif ?>

    <?php if ($donations = $model->donations): ?>
        <section class="campaign-donations-section container">
            <div class="center-section mx-auto pb-5">
                <h2 class="text-uppercase text-center m-0 py-5"><?= Yii::t('campaign', 'Adományozók') ?></h2>
                <div class="row">
                    <div class="col-md-8">
                        <div class="campaign-donations">
                            <?php foreach($model->donations as $donation): ?>
                                <div class="row">
                                    <div class="col-4 col-md-2 text-right text-primary amount">
                                        <div><?= Yii::$app->formatter->asCurrency($donation->amount) ?></div>
                                        <?php if ($donation->recurring): ?>
                                            <div><small class="font-weight-normal"><?= Yii::t('campaign', 'rendszeres') ?></small></div>
                                        <?php else: ?>

                                        <?php endif ?>
                                    </div>
                                    <div class="col-8 col-md-10">
                                        <?php if ($donation->ambassador): ?>
                                            <div class="float-md-right text-primary">
                                                <?= Html::a($donation->ambassador->name, ['campaign/ambassador', 'campaign_slug' => $model->slug, 'slug' => $donation->ambassador->slug]) ?>
                                            </div>
                                        <?php endif ?>
                                        <div class="title float-md-left">
                                            <?php if (!$donation->anonymous): ?>
                                                <strong><?= $donation->name ?></strong>
                                            <?php else: ?>
                                                <strong>Anonim támogatás</strong>
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
                    </div>
                    <div class="col-md-4">
                        <div class="donation-sum text-center sticky-top">
                            <div class="px-4 py-5">
                                <h3 class="text-secondary"><?= Yii::$app->formatter->asCurrency($model->collected) ?></h3>
                                <p class="text-muted"><?= Yii::t('campaign', 'összegyűjtőtt adomány') ?></p>
                                <div class="mt-4">
                                    <div class="text-primary"><strong><?= Yii::t('campaign', 'Köszönjük a segítséget!') ?></strong></div>
                                    <div>
                                        <?= Yii::t('campaign', 'A InDaHouse havi működési költsége ötmillió forint. Ebből 230 Borsodban élő gyereknek tartunk személyre szabott fejlesztést hétről hétre.') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="footer p-4">
                                <a href="#campaign_form_widget" class="btn btn-primary text-uppercase rounded-xl btn-sm">
                                    <?= Yii::t('campaign', 'Én is szeretnék adni') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif ?>
</div>
