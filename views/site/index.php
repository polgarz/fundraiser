<?php

/* @var $this yii\web\View */
/* @var $model app\models\campaign\Campaign */

use app\assets\CampaignAsset;
use app\widgets\DonationFormWidget;
use app\widgets\HighlightedCampaignWidget;
use yii\helpers\Html;

$this->title = Yii::$app->name;

CampaignAsset::register($this);
?>

<div class="mx-auto text-secondary pt-5 px-0 container-fluid">
    <div class="container post-details page">
        <div class="row">
            <div class="col-md-8">

                <?= HighlightedCampaignWidget::widget() ?>

                <?php if ($campaigns): ?>
                    <section class="support-section text-center">
                        <div class="support-section-wrapper center-section mx-auto pb-2">
                            <h2 class="text-uppercase m-0 p-1">Aktuális gyűjtéseink</h2>
                            <?php foreach($campaigns as $campaign): ?>
                                <div class="campaign text-left my-4 my-0">
                                    <div class="cover d-flex align-items-center" style="background-image: url(<?= $campaign->getBehavior('cover')->getThumbUploadUrl('cover_image', 'list') ?>">
                                        <div class="details">
                                            <h3 class="text-uppercase px-4 pt-4 pb-1"><?= $campaign->title ?></h3>
                                            <p class="px-4 m-0 d-none d-md-block">
                                                <?= $campaign->lead ?>
                                            </p>
                                            <?= Html::a(Yii::t('campaign', 'Megnézem'), ['campaign/details', 'slug' => $campaign->slug], ['class' => 'm-4 rounded-xl btn btn-white btn-sm px-3']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </section>
                <?php endif ?>


            </div>

            <div class="col-md-4">
                <?= DonationFormWidget::widget(['model' => $donationFormModel]) ?>
            </div>
        </div>
    </div>
</div>
