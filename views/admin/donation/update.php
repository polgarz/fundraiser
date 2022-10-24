<?php

use app\models\donation\DonationForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\post\Post */

$this->title = 'Adomány módosítása: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Adományok', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];

$this->registerJs('
    function changeAmbassadorList() {
        let campaign_id = $("#donation-campaign_id").val();

        $("#donation-campaign_ambassador_id").find("option").each(function() {
            if ($(this).attr("data-campaign-id") == campaign_id) {
                $(this).removeClass("d-none");
            } else if ($(this).val()) {
                $(this).addClass("d-none");
            }
        });
    }

    $(document).ready(function() { changeAmbassadorList() });
    $("#donation-campaign_id").on("change", function() {
        changeAmbassadorList();
        $("#donation-campaign_ambassador_id").find("option:eq(0)").attr("selected", true);
    });
', View::POS_END);
?>
<div class="post-update">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'campaign_id')->dropdownList($campaignList, ['prompt' => 'Általános adomány']) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'campaign_ambassador_id')->dropdownList(ArrayHelper::map($ambassadorList, 'id', 'name'), [
                'prompt' => 'Nincs',
                'options' => ArrayHelper::map($ambassadorList, 'id', function($model) {
                    return ['data-campaign-id' => $model->campaign_id];
                })
            ]) ?>
        </div>

        <div class="col-md-2">
            <?php if ($model->payment_method === DonationForm::PAYMENT_METHOD_TRANSFER): ?>
                <?= $form->field($model, 'amount', [
                    'inputTemplate' => '<div class="input-group">{input}<div class="input-group-append">
                            <span class="input-group-text">Ft</span>
                        </div></div>'
                ])->textInput(['type' => 'number']) ?>
            <?php endif ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Mentés', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
