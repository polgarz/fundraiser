<?php

/* @var $this yii\web\View */

use app\assets\ErrorAsset;

$this->title = $name;

ErrorAsset::register($this);
?>


<div class="error-template py-8">
    <h2>
        <?= Yii::t('app/error', 'Ajjaj!') ?>
    </h2>
    <h1>
        <?= $name ?>
    </h1>
    <div class="error-details">
        <?= $message ?>
    </div>
    <div class="error-actions">
        <a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary rounded-xl">
            <?= Yii::t('app/error', 'Vissza a kezdőoldalra') ?>
        </a>
    </div>
</div>