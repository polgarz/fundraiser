<?php

use app\models\campaign\Campaign;
use app\models\user\User;
use yii\bootstrap4\LinkPager;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Adománygyűjtő kampányok';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="campaign-index">

    <p>
        <?= Html::a('Új kampány', ['create'], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{summary}{items}',
        'tableOptions' => ['class' => 'table table-striped'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            if ($model->status == Campaign::STATUS_DRAFT) {
                return ['class' => 'table-warning'];
            }
        },
        'columns' => [
            'title',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->statusList[$model->status];
                }
            ],
            'archive:boolean',
            //'content:ntext',
            //'archive',
            //'goal',
            //'meta_description',
            //'og_image',
            //'og_description',
            //'recurring_available',
            //'default_donation_type',
            //'custom_donation_available',
            'highlighted:boolean',
            //'highlight_image',
            //'cover_image',
            //'created_at:datetime',
            //'updated_at',
            //'created_by',
            //'updated_by',
            [
                'label' => 'Összegyűjtött adomány',
                'value' => function($model) {
                    return Yii::$app->user->can(User::GROUP_ADMIN) ? Html::a(Yii::$app->formatter->asCurrency($model->collected), ['/admin/donation/index', 'DonationSearch[campaign_id]' => $model->title]) : Yii::$app->formatter->asCurrency($model->collected);
                },
                'format' => 'raw',
            ],
            [
                'label' => 'Nagykövetek',
                'value' => function($model) {
                    return Html::a(count($model->ambassadors), ['admin/ambassador/index', 'id' => $model->id]);
                },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="text-nowrap">{ambassador} {view} {update} {delete} {link}</div>',
                'buttons' => [
                    'ambassador' => function($url, $model, $key) {
                        return Html::a('<span data-feather="users">Nagykövetek</span>', ['admin/ambassador/index', 'id' => $model->id], ['title' => 'Nagykövetek']);
                    },
                    'update' => function($url, $model, $key) {
                        return Html::a('<span data-feather="edit">Módosítás</span>', $url, ['title' => 'Módosítás']);
                    },
                    'delete' => function($url, $model, $key) {
                        return Html::a('<span data-feather="trash">Törlés</span>', $url, ['data-method' => 'post', 'data-confirm' => 'Biztosan törlöd ezt a kampányt?', 'title' => 'Törlés']);
                    },
                    'link' => function($url, $model, $key) {
                        return Html::a('<span data-feather="external-link">Kampány oldala</span>', ['/campaign/details', 'slug' => $model->slug], ['title' => 'Kampány oldala']);
                    },
                ],
            ],
        ],
    ]); ?>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>
