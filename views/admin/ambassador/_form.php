<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\campaign\CampaignAmbassador */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="campaign-ambassador-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput() ?>

            <?= $form->field($model, 'content')->textarea(['rows' => 25, 'id' => 'description']) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'approved')->checkbox() ?>

            <?= $form->field($model, 'goal', [
                'inputTemplate' => '<div class="input-group">{input}<div class="input-group-append">
                        <span class="input-group-text">Ft</span>
                    </div></div>'
            ])->textInput(['type' => 'number']) ?>

            <?= $form->field($model, 'lead')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'image')->widget(FileInput::class, [
                'options' => [
                    'accept' => 'image/*',
                ],
                'pluginOptions' => [
                    'initialPreview' => [$model->getUploadUrl('image')],
                    'deleteUrl' => Url::to(['delete-image', 'id' => $model->id]),
                    'initialPreviewAsData' => true,
                    'overwriteInitial' => false,
                    'showPreview' => true,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'msgPlaceholder' => '',
                ],
                ])->hint('Ajánlott négyzetes képet feltölteni') ?>

            <?= $form->field($model, 'tshirt_size')->textInput(['disabled' => true]) ?>

            <?= $form->field($model, 'address')->textInput(['disabled' => true]) ?>

            <?= $form->field($model, 'commitment')->textInput(['disabled' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Mentés', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
