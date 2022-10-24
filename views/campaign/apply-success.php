<?php
use app\assets\CampaignAsset;
use kekaadrenalin\recaptcha3\ReCaptchaWidget;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;

CampaignAsset::register($this);

$this->title = Yii::t('campaign/apply', '{campaign} - jelentkezés nagykövetnek', ['campaign' => $model->title]);
?>

<div class="mx-auto text-secondary pt-5 px-0 container-fluid">
    <div class="container page">
        <h1 class="text-uppercase text-left"><?= $this->title ?></h1>
        <h2><?= Yii::t('campaign/apply', 'Sikeres jelentkezés!') ?></h2>

        <div class="py-3">

            <p><?= Yii::t('campaign/apply', 'Köszönjük, hogy jelentkezel nagykövetnek!') ?></p>

            <p><?= Yii::t('campaign/apply', 'Valaki a csapatból hamarosan elbírálja a kampányodat. Ha sikeres volt az elbírálás, külön emailben értesítünk.') ?></p>

            <p><?= Yii::t('campaign/apply', 'Ne feledd! Kampányodat bármikor módosíthatod a <a href="{url}">profil oldaladon</a>', ['url' => Url::to(['user/profile'])]) ?></p>
        </div>

    </div>
</div>