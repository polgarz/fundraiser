<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\campaign\Campaign */

$this->title = 'Új kampány';
$this->params['breadcrumbs'][] = ['label' => 'Kampányok', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="campaign-create">

    <?= $this->render('_form', [
        'model' => $model,
        'tagList' => $tagList,
    ]) ?>

</div>
