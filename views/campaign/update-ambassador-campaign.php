<?php
use app\assets\CampaignAsset;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

CampaignAsset::register($this);
$this->title = Yii::t('campaign/apply', '{campaign} - kampányod szerkesztése', ['campaign' => $model->campaign->title]);
?>

<div class="mx-auto text-secondary pt-5 px-0 container-fluid">
    <div class="container page">
        <h1 class="text-uppercase text-left"><?= $this->title ?></h1>

        <?php $form = ActiveForm::begin(['successCssClass' => '']); ?>

        <?= $form->field($formModel, 'name', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput()->hint(Yii::t('campaign/apply', 'Maximum 35 karakter')) ?>

        <?= $form->field($formModel, 'lead', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput()->hint(Yii::t('campaign/apply', 'Egy rövid leírás, ami összefoglalja, hogy miért szeretnél nekünk gyűjteni (maximum 140 karakter)')) ?>

        <?= $form->field($formModel, 'goal', [
            'labelOptions' => ['class' => 'font-weight-bold'],
            'inputTemplate' => '<div class="input-group">{input}<div class="input-group-append">
                    <span class="input-group-text">Ft</span>
                </div></div>'
        ])->textInput(['type' => 'number']) ?>

        <?php if ($image = $model->getThumbUploadUrl('image', 'list')): ?>
            <div class="mb-2">
                <img src="<?= $image ?>" width="200" />
            </div>
        <?php endif ?>

        <?= $form->field($formModel, 'image', ['labelOptions' => ['class' => 'font-weight-bold']])
            ->fileInput()
            ->hint(Yii::t('campaign/apply', 'Válassz egy jó minőségű képet magadról, ami megjelenik majd a gyűjtesednél. A képnek jpg vagy png formátumúnak kell lennie, és nem lehet nagyobb mint 14 mb'))
            ->label(!isset($model->image))
            ?>

        <?= $form->field($formModel, 'content', ['labelOptions' => ['class' => 'font-weight-bold']])->textArea(['rows' => 15])->hint(Yii::t('campaign/apply', 'Hosszú leírása annak, hogy miért szeretnél nekünk gyűjteni (bármilyen hosszú lehet)')) ?>

        <div class="text-center py-3">
            <?= Html::submitButton(Yii::t('app', 'Mentés'), ['class' => 'btn btn-primary rounded-xl btn-lg']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>