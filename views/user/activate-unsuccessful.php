<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegistrationForm */

use yii\helpers\Html;
use app\assets\UserAsset;

UserAsset::register($this);

$this->title = Yii::t('user', 'Sikertelen aktiválás');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container my-5">
    <h1 class="section__title"><?= $this->title ?></h1>
    <p><?= Yii::t('user', 'Az aktiválás sikertelen volt. Lehet, hogy már korábban aktiváltad a fiókodat') ?></p>
</div>
