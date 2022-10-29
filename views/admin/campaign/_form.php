<?php

use app\models\campaign\Campaign;
use app\models\campaign\CampaignDonationOption;
use kartik\file\FileInput;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use bizley\quill\Quill;

/* @var $this yii\web\View */
/* @var $model app\models\campaign\Campaign */
/* @var $form yii\widgets\ActiveForm */

if ($model->isNewRecord) {
    $this->registerJs("
        $('#campaign-title').keyup(function() {
            $('#campaign-slug').val(slugger($(this).val()));
        });
    ");
}
?>

<div class="campaign-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-md-8">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'lead')->textarea(['rows' => 3])->hint('Ez fog megjelenni a listában') ?>

            <?= $form->field($model, 'content')->widget(Quill::class, [
                'toolbarOptions' => [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote'],
                    [['list' => 'ordered'], [ 'list' => 'bullet']],
                    [['indent' => '-1'], [ 'indent' => '+1']],
                    [['header' => [1, 2, 3, 4, 5, 6, false]]],
                    [['color' => []], [ 'background' => []]],
                    [['align' => []]],
                    ['image', 'video'],
                    ['clean']
                ]
            ]) ?>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center collapsed cursor-pointer" data-toggle="collapse" data-target="#collapseHighlight" aria-expanded="false" aria-controls="collapseHighlight">
                    <strong>Kiemelés</strong>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="card-body collapse" id="collapseHighlight">
                    <?= $form->field($model, 'highlighted')->checkbox()->hint('A kiemelt kampány megjelenik a weboldal különböző részein is. Ha több kiemelt kampány van, akkor oldalbetöltésenként változik, hogy melyiket mutatjuk') ?>

                    <?= $form->field($model, 'highlight_subtitle')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'highlight_text')->textArea(['rows' => 4]) ?>

                    <?= $form->field($model, 'highlight_image')->widget(FileInput::class, [
                        'options' => [
                            'accept' => 'image/*',
                        ],
                        'pluginOptions' => [
                            'initialPreview' => [$model->getBehavior('highlight')->getUploadUrl('highlight_image')],
                            'deleteUrl' => Url::to(['delete-highlight-image', 'id' => $model->id]),
                            'initialPreviewAsData' => true,
                            'overwriteInitial' => false,
                            'showPreview' => true,
                            'showCaption' => true,
                            'showRemove' => true,
                            'showUpload' => false,
                            'msgPlaceholder' => '',
                        ],
                        ])->hint('Ajánlott méret: 1100 x 273px') ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center collapsed cursor-pointer" data-toggle="collapse" data-target="#collapseDonationForm" aria-expanded="false" aria-controls="collapseDonationForm">
                    <strong>Adományozási űrlap</strong>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="card-body collapse" id="collapseDonationForm">
                    <div class="row">
                        <div class="col"><?= $form->field($model, 'recurring_available')->checkbox() ?></div>

                        <div class="col"><?= $form->field($model, 'address_required')->checkbox()->hint('Például hogy ajándékot küldjünk a címre') ?></div>

                        <div class="col"><?= $form->field($model, 'custom_donation_available')->checkbox()->hint('Ha előre meghatározott összegek vannak, egyedi összeget is beírhat az adományozó') ?></div>
                    </div>

                    <?= $form->field($model, 'default_donation_type')->dropDownList($model->donationTypeList) ?>

                    <?= $form->field($model, 'donation_options')->widget(MultipleInput::class, [
                        'addButtonPosition' => MultipleInput::POS_FOOTER,
                        'iconSource' => 'fa',
                        'allowEmptyList' => true,
                        'columns' => [
                            [
                                'name' => 'name',
                                'title' => (new CampaignDonationOption())->getAttributeLabel('name'),
                                'type' => MultipleInputColumn::TYPE_TEXT_INPUT,
                            ],
                            [
                                'name' => 'value',
                                'title' => (new CampaignDonationOption())->getAttributeLabel('value'),
                                'type' => MultipleInputColumn::TYPE_TEXT_INPUT,
                                'options' => ['type' => 'number']
                            ],
                            [
                                'name' => 'order',
                                'title' => (new CampaignDonationOption())->getAttributeLabel('order'),
                                'type' => MultipleInputColumn::TYPE_TEXT_INPUT,
                                'options' => ['type' => 'number']
                            ],
                        ]
                    ])
                    ?>
                </div>
            </div>

        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'status')->dropDownList($model->statusList) ?>

            <?= $form->field($model, 'archive')->checkbox()->hint('Az archív kampányok nem jelennek meg az aktív gyűjtéseink listában, csak az archív oldalon') ?>

            <?= $form->field($model, 'goal', [
                'inputTemplate' => '<div class="input-group">{input}<div class="input-group-append">
                        <span class="input-group-text">Ft</span>
                    </div></div>'
            ])->textInput(['type' => 'number']) ?>

            <?= $form->field($model, 'ambassador_can_apply')->checkbox() ?>

            <?= $form->field($model, 'cover_image')->widget(FileInput::class, [
                'options' => [
                    'accept' => 'image/*',
                ],
                'pluginOptions' => [
                    'initialPreview' => [$model->getBehavior('cover')->getUploadUrl('cover_image')],
                    'deleteUrl' => Url::to(['delete-cover-image', 'id' => $model->id]),
                    'initialPreviewAsData' => true,
                    'overwriteInitial' => false,
                    'showPreview' => true,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'msgPlaceholder' => '',
                ],
                ])->hint('Ez fog megjelenni a listákban, ajánlott méret: 1100 x 275px') ?>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle="collapse" data-target="#collapseSEO" aria-expanded="true" aria-controls="collapseSEO">
                    <strong>SEO</strong>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="card-body show" id="collapseSEO">
                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'disabled' => !$model->isNewRecord && $model->status == Campaign::STATUS_PUBLIC]) ?>

                    <?= $form->field($model, 'tags')->widget(Select2::class, [
                        'options' => ['multiple' => true],
                        'pluginOptions' => [
                            'data' => $tagList,
                            'tags' => true,
                            'tokenSeparators' => [',', ' '],
                        ],
                        ]) ?>

                    <?= $form->field($model, 'meta_description')->textArea() ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center collapsed cursor-pointer" data-toggle="collapse" data-target="#collapseShare" aria-expanded="false" aria-controls="collapseShare">
                    <strong>Megosztási beállítások</strong>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="card-body collapse" id="collapseShare">
                    <?= $form->field($model, 'og_image')->widget(FileInput::class, [
                        'options' => [
                            'accept' => 'image/*',
                        ],
                        'pluginOptions' => [
                            'initialPreview' => [$model->getBehavior('og')->getUploadUrl('og_image')],
                            'deleteUrl' => Url::to(['delete-og-image', 'id' => $model->id]),
                            'initialPreviewAsData' => true,
                            'overwriteInitial' => false,
                            'showPreview' => true,
                            'showCaption' => true,
                            'showRemove' => true,
                            'showUpload' => false,
                            'msgPlaceholder' => '',
                        ],
                        ])->hint('Ajánlott méret: 1200px x 630px') ?>

                    <?= $form->field($model, 'og_description')->textArea()->hint('Ez fog megjelenni megosztásnál') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Mentés', ['name' => 'preview', 'value' => 0, 'class' => 'btn btn-success']) ?>

        <?= Html::submitButton('Előnézet', ['name' => 'preview', 'value' => 1, 'class' => 'btn btn-secondary', 'formtarget' => '_blank']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
