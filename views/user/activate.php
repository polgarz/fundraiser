<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegistrationForm */

use yii\helpers\Html;
use app\assets\UserAsset;

UserAsset::register($this);

$this->title = Yii::t('user', 'Sikeres regisztráció!');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container my-5">
    <h1><?= $this->title ?></h1>
    <p><?= Yii::t('user', 'Regisztrációd aktiválásához kattints az emailben küldött linkre!') ?></p>
</div>
