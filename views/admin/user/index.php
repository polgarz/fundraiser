<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap4\LinkPager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Felhasználók';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <p>
        <?= Html::a('Új felhasználó', ['create'], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{summary}{items}',
            'tableOptions' => ['class' => 'table table-striped'],
            'columns' => [
                'first_name',
                'last_name',
                'email:email',
                [
                    'attribute' => 'group',
                    'filter' => array_combine(Yii::$app->authManager->defaultRoles, Yii::$app->authManager->defaultRoles),
                ],
                //'phone',
                //'profile_image',
                //'description:ntext',
                //'invoice_name',
                //'invoice_zip',
                //'invoice_city',
                //'invoice_address_line',
                //'group',
                //'password',
                //'auth_key',
                //'activation_hash',
                //'password_reset_code',
                //'updated_at',
                //'created_by',
                //'updated_by',
                'created_at:datetime',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '<div class="text-nowrap">{update} {delete} {activate}</div>',
                    'buttons' => [
                        'update' => function($url, $model, $key) {
                            return Html::a('<span data-feather="edit">Módosítás</span>', $url, ['title' => 'Módosítás']);
                        },
                        'delete' => function($url, $model, $key) {
                            return Html::a('<span data-feather="trash">Törlés</span>', $url, ['data-method' => 'post', 'data-confirm' => 'Biztosan törlöd ezt a felhasználót?', 'title' => 'Törlés']);
                        },
                        'activate' => function($url, $model, $key) {
                            if ($model->activation_hash) {
                                return Html::a('<span data-feather="check-square">Aktiválás</span>', $url, ['title' => 'Aktiválás']);
                            }
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>
