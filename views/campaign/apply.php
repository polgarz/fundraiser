<?php
use app\assets\CampaignAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

CampaignAsset::register($this);

$this->title = Yii::t('campaign/apply', '{campaign} - jelentkezés nagykövetnek', ['campaign' => $model->title]);
?>

<div class="mx-auto text-secondary pt-5 px-0 container-fluid">
    <div class="container page">
        <h1 class="text-uppercase text-left"><?= $this->title ?></h1>

        <h2 class="py-3"><?= Yii::t('campaign/apply', 'Kedves {name}!', ['name' => Yii::$app->user->identity->last_name]) ?></h2>

        <p><strong><?= Yii::t('campaign/apply', 'Köszönjük, hogy jelentkezel nagykövetnek!') ?></strong></p>

        <p><?= Yii::t('campaign/apply', 'Ne feledd! Kampányodat bármikor módosíthatod a <a href="{url}">profil oldaladon</a>', ['url' => Url::to(['user/profile'])]) ?></p>

        <hr />

        <?php $form = ActiveForm::begin(['successCssClass' => '']); ?>

        <div class="row">
            <div class="col-md-7">
                <?= $form->field($applyForm, 'name', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput()->hint(Yii::t('campaign/apply', 'Maximum 35 karakter')) ?>
            </div>
            <div class="col-md-5">
                <?= $form->field($applyForm, 'goal', [
                    'labelOptions' => ['class' => 'font-weight-bold'],
                    'inputTemplate' => '
                        <div class="input-group">
                            {input}
                            <div class="input-group-append">
                                <span class="input-group-text">Ft</span>
                            </div>
                        </div>'
                ])->textInput(['type' => 'number']) ?>
            </div>
        </div>

        <?= $form->field($applyForm, 'lead', ['labelOptions' => ['class' => 'font-weight-bold']])->textInput()->hint(Yii::t('campaign/apply', '100-120 karakter, ez fog megjelenni a kampány főoldalán.')) ?>

        <?= $form->field($applyForm, 'image', ['labelOptions' => ['class' => 'font-weight-bold']])->fileInput()->hint(Yii::t('campaign/apply', 'Válassz egy jó minőségű képet magadról, ami megjelenik majd a gyűjtesednél. A képnek jpg vagy png formátumúnak kell lennie, és nem lehet nagyobb mint 14 mb')) ?>

        <?= $form->field($applyForm, 'content', ['labelOptions' => ['class' => 'font-weight-bold']])->textArea(['rows' => 15])->hint(Yii::t('campaign/apply', 'Hosszú leírása annak, hogy miért szeretnél nekünk gyűjteni (bármilyen hosszú lehet)')) ?>

        <?= $form->field($applyForm, 'privacy')->checkbox()->label(Yii::t('campaign/apply', '<strong>Az <a href="{url}">adatvédelmi tájékoztatót</a> elolvastam, megértettem, és elfogadom az abban foglaltakat</strong>', ['url' => '/adatvedelem'])) ?>

        <div class="text-center py-3">
            <?= Html::submitButton(Yii::t('campaign/apply', 'Jelentkezem'), ['class' => 'btn btn-primary rounded-xl btn-lg']) ?>
        </div>
        <?php ActiveForm::end() ?>

    </div>
</div>
