<?php
namespace app\widgets;

use Yii;
use yii\base\Widget;

class DonationFormWidget extends Widget
{
    /**
     * Form model
     *
     * @var Model
     */
    public $model;

    /**
     * Form label
     *
     * @var string
     */
    public $label;

    /**
     * Recurring info url
     *
     * @var string|array
     */
    public $recurringInfoUrl = ['donation/recurring'];

    public function init()
    {
        if (!$this->model) {
            throw new \Exception('Nincs megadva form model');
        }

        if (!$this->label) {
            $this->label = Yii::t('campaign/donation-form', 'Támogass!');
        }

        parent::init();
    }

    public function run()
    {
        return $this->render('donation-form', [
            'model' => $this->model,
            'label' => $this->label,
            'recurringInfoUrl' => $this->recurringInfoUrl,
            ]);
    }
}