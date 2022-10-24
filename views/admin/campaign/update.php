<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\campaign\Campaign */

$this->title = 'Kampány módosítása: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Kampányok', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title];
?>
<div class="campaign-update">

    <?= $this->render('_form', [
        'model' => $model,
        'tagList' => $tagList,
    ]) ?>

</div>
