<?php

use app\models\campaign\Campaign;
use yii\bootstrap4\LinkPager;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Nagykövetek';
$this->params['breadcrumbs'][] = ['label' => 'Kampányok', 'url' => ['admin/campaign/index']];
$this->params['breadcrumbs'][] = ['label' => $model->title];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="campaign-index">

    <p>
        <?= Html::a('Exportálás', ['export', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{summary}{items}',
        'tableOptions' => ['class' => 'table table-striped'],
        'columns' => [
            'name',
            'goal:currency',
            'collected:currency',
            'updated_at:datetime',
            'created_at:datetime',
            'approved:boolean',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="text-nowrap">{approve} {view} {update} {delete} {link}</div>',
                'buttons' => [
                    'approve' => function($url, $model, $key) {
                        if (!$model->approved) {
                            return Html::a('<span data-feather="check-square">Aktiválás</span>', $url, ['title' => 'Aktiválás', 'data-method' => 'post']);
                        }
                    },
                    'update' => function($url, $model, $key) {
                        return Html::a('<span data-feather="edit">Módosítás</span>', $url, ['title' => 'Módosítás']);
                    },
                    'delete' => function($url, $model, $key) {
                        return Html::a('<span data-feather="trash">Törlés</span>', $url, ['data-method' => 'post', 'data-confirm' => 'Biztosan törlöd ezt a nagykövetet?', 'title' => 'Törlés']);
                    },
                    'view' => function($url, $model, $key) {
                        return Html::a('<span data-feather="eye">Megtekintés</span>', $url, ['title' => 'Megtekintés']);
                    },
                    'link' => function($url, $model, $key) {
                        if ($model->approved) {
                            return Html::a('<span data-feather="external-link">Kampány oldala</span>', ['/campaign/ambassador', 'slug' => $model->slug, 'campaign_slug' => $model->campaign->slug], ['title' => 'Kampány oldala']);
                        }
                    },
                ],
            ],
        ],
    ]); ?>

    <?php if ($dataProvider->pagination): ?>
        <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
    <?php endif ?>
</div>
