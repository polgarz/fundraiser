<?php

use app\models\donation\Donation;
use app\models\donation\DonationForm;
use app\models\donation\DonationSearch;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\donation\DonationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Adományok';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="donation-index">

    <p>
        <?= Html::a('Exportálás', array_merge(['export'], Yii::$app->request->queryParams), ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{summary}{items}',
            'tableOptions' => ['class' => 'table table-striped table-sm'],
            'rowOptions' => function ($model, $key, $index, $grid) {
                if ($model->status == Donation::STATUS_FINISHED) {
                    return ['class' => 'table-success'];
                } else if ($model->payment_method == DonationForm::PAYMENT_METHOD_TRANSFER && $model->status != Donation::STATUS_FINISHED) {
                    return ['class' => 'table-warning'];
                }
            },
            'columns' => [
                ['attribute' => 'id', 'headerOptions' => ['style' => 'width: 60px']],
                [
                    'filter' => $searchModel->paymentMethodList,
                    'attribute' => 'payment_method',
                    'value' => function($model) {
                        return $model->paymentMethodList[$model->payment_method];
                    },
                    'headerOptions' => ['style' => 'width: 98px'],
                ],
                'amount:currency',
                ['attribute' => 'name', 'value' => function($model) { return Html::a($model->name, 'mailto:' . $model->email); }, 'format' => 'raw'],
                'anonymous:boolean',
                [
                    'attribute' => 'campaign_id',
                    'value' => function($model) { return $model->campaign->title ?? 'Általános adomány'; },
                    'filter' => [DonationSearch::GENERAL_DONATION => 'Általános adomány'] + ArrayHelper::map($campaignList, 'id', 'title'),
                ],
                ['attribute' => 'campaign_ambassador_id', 'value' => function($model) { return $model->ambassador->name ?? null; }],
                [
                    'attribute' => 'status',
                    'filter' => $searchModel->statusList,
                    'value' => function($model) {
                        return $model->statusList[$model->status];
                    }
                ],
                [
                    'attribute' => 'recurring',
                    'filter' => [1 => 'Igen', 0 => 'Nem'],
                    'value' => function($model) {
                        if ($model->recurring && !$model->parent_id) {
                            $ret = '<div class="text-nowrap">Igen (' . ($model->hasActiveTokens ? 'aktív' : 'inaktív') . ')</div>';
                            $ret .= '<div><small>' . $model->recurringSequenceNumber . '. alkalom</small></div>';

                            return $ret;
                        } elseif ($model->recurring) {
                            $ret = '<div class="text-nowrap">Igen (' . ($model->parent->hasActiveTokens ? 'aktív' : 'inaktív') . ')</div>';
                            $ret .= '<div><small>' . $model->recurringSequenceNumber . '. alkalom</small></div>';

                            return $ret;
                        } else {
                            return 'Nem';
                        }
                    },
                    'format' => 'html',
                ],
                //'parent_id',
                //'token',
                //'token_due_date',
                [
                    'attribute' => 'created_at',
                    'filter' => DateRangePicker::widget(['model' => $searchModel, 'attribute' => 'created_at']),
                    'value' => function($model) {
                        return Yii::$app->formatter->asDateTime($model->created_at);
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '<div class="text-nowrap">{view} {update} {delete} {approve}</div>',
                    'buttons' => [
                        'approve' => function($url, $model, $key) {
                            if ($model->payment_method == DonationForm::PAYMENT_METHOD_TRANSFER && $model->status != Donation::STATUS_FINISHED) {
                                return Html::a('<span data-feather="check-square">Jóváhagyás</span>', $url, ['title' => 'Jóváhagyás', 'data-method' => 'post']);
                            }
                        },
                        'update' => function($url, $model, $key) {
                            return Html::a('<span data-feather="edit">Részletek</span>', $url, ['title' => 'Részletek']);
                        },
                        'view' => function($url, $model, $key) {
                            return Html::a('<span data-feather="eye">Részletek</span>', $url, ['title' => 'Részletek']);
                        },
                        'delete' => function($url, $model, $key) {
                            if ($model->status === Donation::STATUS_TRANSFER_ADDED && $model->payment_method === DonationForm::PAYMENT_METHOD_TRANSFER) {
                                return Html::a('<span data-feather="trash">Törlés</span>', $url, ['title' => 'Törlés', 'data-method' => 'post', 'data-confirm' => 'Biztosan törlöd?']);
                            }
                        },
                    ],
                ],
            ],
        ]) ?>
    </div>
    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>

</div>
