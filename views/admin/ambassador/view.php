<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\campaign\CampaignAmbassador */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Kampányok', 'url' => ['admin/campaign/index']];
$this->params['breadcrumbs'][] = ['label' => $model->campaign->title];
$this->params['breadcrumbs'][] = ['label' => 'Nagykövetek', 'url' => ['index', 'id' => $model->campaign_id]];
$this->params['breadcrumbs'][] = ['label' => $this->title];
\yii\web\YiiAsset::register($this);
?>
<div class="campaign-ambassador-view">

    <p>
        <?= Html::a('Módosítás', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Törlés', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Biztosan törlöd ezt a nagykövetet?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'lead:ntext',
            [
                'attribute' => 'image',
                'value' => function($model) {
                    return Html::img($model->getUploadUrl('image'), ['class' => 'w-25']);
                },
                'format' => 'html',
            ],
            'goal:currency',
            'commitment',
            ['attribute' => 'content', 'value' => function($model) { return nl2br($model->content); }, 'format' => 'html'],
            'approved:boolean',
            'user.email',
            'user.first_name',
            'user.last_name',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute' => 'created_by',
                'value' => function($model) {
                    return $model->createdBy->fullname ?? null;
                }
            ],
            [
                'attribute' => 'updated_by',
                'value' => function($model) {
                    return $model->updatedBy->fullname ?? null;
                }
            ],
        ],
    ]) ?>

</div>
