<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\campaign\CampaignAmbassador */

$this->title = 'Nagykövet kampányának módosítása: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kampányok', 'url' => ['admin/campaign/index']];
$this->params['breadcrumbs'][] = ['label' => $model->campaign->title];
$this->params['breadcrumbs'][] = ['label' => 'Nagykövetek', 'url' => ['index', 'id' => $model->campaign_id]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Módosítás';
?>
<div class="campaign-ambassador-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
