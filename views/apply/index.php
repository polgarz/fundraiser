<?php
use app\assets\ApplyAsset;
use app\models\apply\ApplyForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

ApplyAsset::register($this);

$this->title = 'Jelentkezés önkéntesnek';
?>

<div class="mx-auto text-secondary pt-5 px-0 container-fluid">
    <div class="container page">
        <h1 class="text-uppercase text-left">Legyél az önkéntesünk!</h1>

        <p class="py-3">
            Csatlakozz az InDaHouse Hungary önkénteseinek csapatához és tegyél azért, hogy Magyarország egy igazságosabb, sokszínűbb hely legyen, ahol nemcsak azoknak van esélye, akik jó körülmények közé születtek, hanem a kisfalvakban élő, hátrányos helyzetű gyerekeknek is! Légy az önkéntesünk, és segíts a gyerekekkel vagy a háttérben! Mindenkire szükségünk van, mindenki építeni tudja az InDaHouse-t! Töltsd ki az alábbi jelentkezési lapot, hogy értesítést tudjunk küldeni a következő felvételi lehetőségről.
        </p>

        <hr />

        <?php $form = ActiveForm::begin(['successCssClass' => '']); ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput(['placeholder' => 'Példa Rudolf']) ?>

                    <?= $form->field($model, 'email', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput(['placeholder' => 'onkentesleszek@indahousehungary.hu']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'phone', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput(['placeholder' => '+36 70 123 4567']) ?>

                    <?= $form->field($model, 'age', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput(['type' => 'number', 'placeholder' => 31]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'address', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput(['placeholder' => 'Budapest']) ?>

                    <?= $form->field($model, 'job', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput() ?>

                    <?= $form->field($model, 'inform', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput() ?>

                    <?= $form->field($model, 'about', ['labelOptions' => ['class' => 'font-weight-bold']])->textArea(['rows' => 6]) ?>

                    <?= $form->field($model, 'experience', ['labelOptions' => ['class' => 'font-weight-bold']])->textArea(['rows' => 6]) ?>

                    <?= $form->field($model, 'spend', ['labelOptions' => ['class' => 'font-weight-bold']])->textArea(['rows' => 6]) ?>

                    <?= $form->field($model, 'help', ['labelOptions' => ['class' => 'font-weight-bold']])->radioList($model->helpList, [
                        'item' => function ($index, $label, $name, $checked, $value) use (&$model, &$form) {
                            return '
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="i' . $index . '" class="custom-control-input" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'CHECKED' : '') . '>
                                    <label class="custom-control-label" for="i' . $index . '">
                                        <strong>' . $label . '</strong>
                                        ' . ($value == ApplyForm::HELP_OTHER ? $form->field($model, 'other_text')->textArea(['rows' => 3, 'class' => 'form-control mt-2'])->label(false)->hint(false) : '') . '
                                        <div class="help-block mb-2 mt-1 text-muted">'. ($model->helpHint[$value] ?? '') . '</div>
                                    </label>
                                </div>
                            ';
                        }
                    ]) ?>
                </div>
            </div>

            <?= $form->field($model, 'privacy')->checkbox()->label('<strong>Az <a href="/adatvedelem">adatvédelmi tájékoztatót</a> elolvastam, megértettem, és elfogadom az abban foglaltakat</strong>') ?>

            <div class="text-center py-3">
                <?= Html::submitButton('Jelentkezem', ['class' => 'btn btn-primary btn-lg']) ?>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>