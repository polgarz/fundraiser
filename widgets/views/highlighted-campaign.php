<?php
use yii\helpers\Url;
?>
<section class="bg-gradient campaign-volunteers-section text-left">
    <div class="center-section mx-auto">
        <div class="highlighted-campaign" style="background-image: url(<?= $model->getBehavior('highlight')->getThumbUploadUrl('highlight_image', 'main') ?>)">
            <div class="p-4">
                <h2 class="text-uppercase pb-0 mb-1"><?= $model->title ?></h2>
                <?php if ($model->highlight_subtitle): ?>
                    <h3 class="text-uppercase pt-0 mt-0"><?= $model->highlight_subtitle ?></h3>
                <?php endif ?>

                <p class="my-3 d-none d-md-block">
                    <?= $model->highlight_text ?>
                </p>

                <div class="d-none d-md-block">
                    <a class="btn btn-primary rounded-xl px-3 mr-2 btn-sm" href="<?= Url::to(['campaign/details', 'slug' => $model->slug]) ?>">Támogatom</a>
                    <?php if ($model->highlighted): ?>
                        <a class="rounded-xl btn btn-white px-3 btn-sm" href="<?= Url::to(['campaign/apply', 'slug' => $model->slug]) ?>">Én is szeretnék gyűjteni</a>
                    <?php endif ?>
                </div>
                <div class="d-block d-md-none">
                    <div class="mb-2"><a class="btn btn-primary rounded-xl px-3 mr-2" href="<?= Url::to(['campaign/details', 'slug' => $model->slug]) ?>">Támogatom</a></div>
                    <?php if ($model->highlighted): ?>
                        <div><a class="rounded-xl btn btn-white px-3" href="<?= Url::to(['campaign/apply', 'slug' => $model->slug]) ?>">Én is szeretnék gyűjteni</a></div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</section>
