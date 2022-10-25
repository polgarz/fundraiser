<?php

use app\assets\CampaignAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

CampaignAsset::register($this);

$this->title = 'Átutalásos támogatás';

$this->registerJs("
    var dataLayer  = window.dataLayer || [];
    dataLayer.push(" . Json::encode($model->dataLayer) . ");
", View::POS_BEGIN);
?>
<div class="mx-auto text-secondary pt-5 px-0 container-fluid">
    <div class="container post-details page">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-uppercase text-left"><?= Html::encode($this->title) ?></h1>

                <p>
                    Köszönjük, hogy jelezted, hogy támogatod az Egyesületünket!
                </p>

                <p>
                    <strong>Az adományozáshoz szükséges adatok:</strong><br />
                    <strong>Név: </strong> <?= Yii::$app->params['transfer']['name'] ?><br />
                    <strong>Számlaszám: </strong> <?= Yii::$app->params['transfer']['bank_account_nr'] ?> (<?= Yii::$app->params['transfer']['bank_name'] ?>)
                    <br />
                    A közlemény mezőbe a következő kódot írd: #<?= $model->id ?>
                </p>

                <p><strong>Fontos:</strong> a felületen akkor jelenik meg az adományod, ha elutaltad, és az egyik tagunk jóváhagyta azt!</p>

                <p><strong>Köszönjük!</strong></p>
            </div>
        </div>
    </div>
</div>
