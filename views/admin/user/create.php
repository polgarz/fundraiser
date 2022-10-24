<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\user\User */

$this->title = 'Új felhasználó';
$this->params['breadcrumbs'][] = ['label' => 'Felhasználók', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
