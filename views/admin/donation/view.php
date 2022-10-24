<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\donation\Donation */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Adományok', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="donation-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'user_id',
                'value' => function($model) {
                    if ($model->user) {
                        return Html::a($model->user->fullname, ['admin/user/update', 'id' => $model->user_id]);
                    }
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'payment_method',
                'value' => function($model) {
                    return $model->paymentMethodList[$model->payment_method];
                }
            ],
            'parent_id',
            'amount:currency',
            'name',
            'anonymous:boolean',
            'email:email',
            'message',
            'zip',
            'city',
            'street',
            'note',
            'newsletter:boolean',
            [
                'attribute' => 'campaign_id',
                'value' => function($model) {
                    return $model->campaign->title ?? 'Általános adomány';
                }
            ],
            [
                'attribute' => 'campaign_ambassador_id',
                'value' => function($model) {
                    return $model->ambassador->name ?? null;
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->statusList[$model->status];
                }
            ],
            'vendor_ref',
            'recurring:boolean',
            'created_at:datetime',
        ],
    ]) ?>

</div>
