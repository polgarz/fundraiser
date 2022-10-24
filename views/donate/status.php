<?php
/* @var $model app\models\donation\Donation */
/* @var $message string */

use app\assets\CampaignAsset;
use app\models\donation\Donation;
use yii\helpers\Json;
use yii\web\View;

CampaignAsset::register($this);

$this->title = 'Fizetési státusz';

if ($model->status == Donation::STATUS_SUCCESS) {
    $this->registerJs("
        var dataLayer  = window.dataLayer || [];
        dataLayer.push(" . Json::encode($model->dataLayer) . ");
    ", View::POS_BEGIN);
}
?>

<section class="section payment-status">
    <div class="container py-5">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6 text-center">
                <?php if ($model->status == Donation::STATUS_SUCCESS): ?>
                    <h2 class="h1">Sikeres tranzakció</h2>
                    <p class="h4">
                        SimplePay tranzakció azonosító: <?= $transactionId ?><br /><br />
                    </p>
                <?php else: ?>
                    <h2 class="h1">Sikertelen tranzakció</h2>
                    <p class="h4">
                        SimplePay tranzakció azonosító: <?= $transactionId ?><br /><br />

                        <?php if ($model->status == Donation::STATUS_ERROR): ?>
                            Kérjük, ellenőrizd a tranzakció során megadott adatok helyességét.
                            Amennyiben minden adatot helyesen adtál meg,
                            a visszautasítás okának kivizsgálása kapcsán kérjük, lépj kapcsolatba kártyakibocsátó bankoddal.
                        <?php elseif ($model->status == Donation::STATUS_TIMEOUT): ?>
                            Időtúllépés miatt sikertelen.
                        <?php elseif ($model->status == Donation::STATUS_CANCELED): ?>
                            Tranzakció megszakítva!
                        <?php elseif ($model->status == Donation::STATUS_UNKNOWN): ?>
                            Ismeretlen válasz! Lépj velünk kapcsolatba elérhetőségeink valamelyikén!
                        <?php endif ?>

                        <?php if ($message): ?>
                            <p class="h4">Hibaüzenet: <?= $message ?></p>
                        <?php endif ?>
                    </p>
                <?php endif ?>

            </div>
        </div>
    </div>
</section>
