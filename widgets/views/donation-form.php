<?php

use app\models\donation\DonationForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

if ($model->donation_options) {
    $this->registerJs("
        var donation_options = document.getElementsByName('DonationForm[donation_option]');
        var amount = document.getElementById('donationform-amount');
        var donation_option_custom_amount = document.getElementById('donation-option-other_value');

        for (var i = 0, length = donation_options.length; i < length; i++) {
            donation_options[i].addEventListener('change', function() {
                if (this.checked) {
                    if (this.value == 'other') {
                        amount.value = donation_option_custom_amount.value;
                    } else {
                        amount.value = this.value;
                    }
                }
            });
        }
    ", View::POS_END);

    if ($model->custom_donation_available) {
        $this->registerJs("
            donation_option_custom_amount.addEventListener('keyup', function() {
                document.getElementById('donation-option-other').checked = 1;
                amount.value = this.value;
            });
        ", View::POS_END);
    }
}

if ($model->recurring_available) {
    $this->registerJs("
        var donation_type_one_time = document.getElementById('donation_type-one-time');
        var donation_type_recurring = document.getElementById('donation_type-recurring');
        var payment_method_container_transfer = document.getElementById('payment_method_container-transfer');
        var payment_method_card = document.getElementById('payment_method-card');
        var payment_method_transfer = document.getElementById('payment_method-transfer');
        var card_registration_checkbox_container = document.querySelector('.field-donationform-card_registration_policy');

        donation_type_one_time.addEventListener('change', function() {
            if (this.checked) {
                payment_method_container_transfer.classList.remove('d-none');
            }
            card_registration_checkbox_container.classList.add('d-none');
        });

        donation_type_recurring.addEventListener('change', function() {
            if (this.checked) {
                payment_method_container_transfer.classList.add('d-none');
            }
            if (payment_method_transfer.checked) {
                payment_method_card.checked = 1;
            }
            card_registration_checkbox_container.classList.remove('d-none');
        });
    ", View::POS_END);
}
?>

<a name="campaign_form_widget"></a>
<div class="donate-form-widget mb-5">
    <h3><?= $label ?></h3>
    <?php $form = ActiveForm::begin(['successCssClass' => '', 'enableClientValidation' => false, 'enableClientScript' => false, 'options' => ['onsubmit' => 'donation_form_submit_btn.disabled = true; return true;']]) ?>

    <?php if ($model->donation_options): ?>
        <?= $form->field($model, 'donation_option')->radioList($model->donation_options, ['encode' => false, 'item' => function($index, $label, $name, $checked, $value) use (&$model) {
            if ($value == 'other') {
                return '
                    <div class="form-check">
                        <input type="radio" id="donation-option-other" class="form-check-input" name="' . $name . '" value="other" ' . ($checked ? 'CHECKED' : '') . '>
                        <label class="form-check-label" for="donation-option-other">
                            <div class="mb-n2 font-weight-bold" style="font-size: 14px; color: white">' . Yii::t('campaign/donation-form', 'egyéb összeg') . '</div>
                        </label>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" name="custom_amount" value="' . Yii::$app->request->post('custom_amount', '') . '" id="donation-option-other_value" style="margin-top: 5px; height: 28px; margin-left: 20px; width: auto; ">
                    </div>
                ';
            } else {
                return '
                    <div class="form-check">
                        <input type="radio" id="donation-option-' . $index . '" class="form-check-input" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'CHECKED' : '') . '>
                        <label class="form-check-label" for="donation-option-' . $index . '">
                            <div class="mb-n2 font-weight-bold" style="font-size: 14px; color: white">' . Yii::$app->formatter->asCurrency($value) . '</div><div class="text-lowercase">' . $label . '</div>
                        </label>
                    </div>
                ';
            }
        }]) ?>
        <?= $form->field($model, 'amount')->hiddenInput()->label(false) ?>
        <?php if ($error = $model->getFirstError('amount')): ?>
            <div class="invalid-feedback d-block"><?= $error ?></div>
        <?php endif ?>
    <?php else: ?>
        <?= $form->field($model, 'amount')->textInput(['maxlength' => true, 'type' => 'number']) ?>
    <?php endif ?>

    <hr />

    <?php if ($model->recurring_available): ?>
        <?= $form->field($model, 'donation_type')->radioList($model->donationTypeList, ['encode' => false, 'item' => function($index, $label, $name, $checked, $value) {
            return '
                <div class="form-check">
                    <input type="radio" id="donation_type-' . $value . '" class="form-check-input" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'CHECKED' : '') . '>
                    <label class="form-check-label" for="donation_type-' . $value . '">
                        <div class="mb-n2 font-weight-bold" style="font-size: 14px; color: white">' . $label . ' ' . ($value == DonationForm::DONATION_TYPE_RECURRING ? Html::a('<small>' . Yii::t('campaign/donation-form', 'mi ez?') . '</small>', Yii::$app->params['recurringPaymentInfoUrl']) : '')  . '</div>
                    </label>
                </div>
            ';
        }])->label(false) ?>
        <hr />
    <?php endif ?>

    <?= $form->field($model, 'payment_method')->radioList($model->paymentMethodList, ['encode' => false, 'item' => function($index, $label, $name, $checked, $value) use (&$model) {
        return '
            <div id="payment_method_container-' . $value . '" class="form-check ' . ($value == DonationForm::PAYMENT_METHOD_TRANSFER && $model->donation_type == DonationForm::DONATION_TYPE_RECURRING ? 'd-none' : '') . '">
                <input type="radio" id="payment_method-' . $value . '" class="form-check-input" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'CHECKED' : '') . '>
                <label class="form-check-label" for="payment_method-' . $value . '">
                    <div class="mb-n2 font-weight-bold" style="font-size: 14px; color: white">' . $label . '</div>
                </label>
            </div>
        ';
    }])->label(false) ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'anonymous', [
            'checkOptions' => ['class' => 'form-check-input']
        ])->checkbox([
            'labelOptions' => ['class' => 'form-check-label']
        ]) ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'message')->textArea(['rows' => 3]) ?>

    <hr />

    <?php if ($model->address_required): ?>

    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'zip')->textInput() ?>
        </div>
            <div class="col-8">
            <?= $form->field($model, 'city')->textInput() ?>
        </div>
    </div>

    <?= $form->field($model, 'street')->textInput() ?>

    <?= $form->field($model, 'note')->textInput() ?>

    <hr />

    <?php endif ?>

    <?= $form->field($model, 'privacy_policy', [
            'checkOptions' => ['class' => 'form-check-input'],
        ])->checkbox([
            'labelOptions' => ['class' => 'form-check-label']
        ])->label(Html::a($model->getAttributeLabel('privacy_policy'), Yii::$app->params['privacyPolicyUrl'])) ?>

    <?php if ($model->recurring_available): ?>
        <?= $form->field($model, 'card_registration_policy', [
                'options' => ['class' => $model->donation_type == DonationForm::DONATION_TYPE_ONE_TIME ? 'd-none' : ''],
                'checkOptions' => ['class' => 'form-check-input']
            ])->checkbox([
                'labelOptions' => ['class' => 'form-check-label']
            ])->label(Html::a($model->getAttributeLabel('card_registration_policy'), Yii::$app->params['cardRegistrationPolicyUrl'])) ?>
    <?php endif ?>

    <?= $form->field($model, 'newsletter', [
            'checkOptions' => ['class' => 'form-check-input']
        ])->checkbox([
            'labelOptions' => ['class' => 'form-check-label']
        ])->label($model->getAttributeLabel('newsletter')) ?>

    <div class="py-4 footer-btns">
        <div class="submit-btn d-flex justify-content-between align-items-center">
            <p class="submit">
                <?= Html::submitButton(Yii::t('campaign/donation-form', 'Támogatás'), ['class' => 'btn-white button button-primary rounded-xl btn btn-primary', 'name' => 'donation_form_submit_btn']) ?>
            </p>
            <a href="https://simplepartner.hu/PaymentService/Fizetesi_tajekoztato.pdf" target="_blank">
                <img src="/dist/img/simplepay.png" title="<?= Yii::t('campaign/donation-form', 'SimplePay - Online bankkártyás fizetés') ?>" alt="<?= Yii::t('campaign/donation-form', 'SimplePay vásárlói tájékoztató') ?>">
            </a>
        </div>
    </div>

    <?php ActiveForm::end() ?>

</div>
