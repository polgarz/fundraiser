<?php

namespace app\models\donation;

use app\models\campaign\Campaign;
use DrewM\MailChimp\MailChimp;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Login form
 *
 * @property User|null $user This property is read-only.
 *
 */
class DonationForm extends Model
{
    /**
     * Donation hash after save
     * This property is inaccessible by users
     *
     * @var string|array
     */
    public $hash;

    /**
     * Is recurring payment available?
     * This property is inaccessible by users
     *
     * @var boolean
     */
    public $recurring_available = true;

    /**
     * Default donation type (recurring, or one-time)
     * This property is inaccessible by users
     *
     * @var string
     */
    public $default_donation_type = Campaign::DONATION_TYPE_ONE_TIME;

    /**
     * Can users set custom donation amount
     * This property is inaccessible by users
     *
     * @var boolean
     */
    public $custom_donation_available = true;

    /**
     * If it's true, users must have fill address data
     *
     * @var boolean
     */
    public $address_required = false;

    /**
     * Campaign id (optional)
     * This property is inaccessible by users
     *
     * @var integer
     */
    public $campaign_id;

    /**
     * Ambassador id (optional)
     * This property is inaccessible by users
     *
     * @var integer
     */
    public $ambassador_id;

    /**
     * Available donation options
     * This property is inaccessible by users
     *
     * @var array (key: amount, value: name)
     */
    public $donation_options = [];

    /**
     * Selected donation option. We don't use it actively, just for design reasons
     *
     * @var integer|string
     */
    public $donation_option;

    /**
     * Donation amount (price)
     *
     * @var integer
     */
    public $amount;

    /**
     * Donation type (one-time, recurring)
     *
     * @var string
     */
    public $donation_type;

    /**
     * Payment method (transfer, card)
     *
     * @var string
     */
    public $payment_method;

    /**
     * Name
     *
     * @var string
     */
    public $name;

    /**
     * Anonymous donation
     *
     * @var boolean
     */
    public $anonymous;

    /**
     * Email address
     *
     * @var string
     */
    public $email;

    /**
     * Donation message
     *
     * @var string
     */
    public $message;

    /**
     * Zip number
     *
     * @var integer
     */
    public $zip;

    /**
     * City
     *
     * @var string
     */
    public $city;

    /**
     * Street
     *
     * @var string
     */
    public $street;

    /**
     * Other note (for address)
     *
     * @var string
     */
    public $note;

    /**
     * Can we send newsletters?
     *
     * @var boolean
     */
    public $newsletter;

    /**
     * User accepted the privacy policy
     *
     * @var boolean
     */
    public $privacy_policy;

    /**
     * User accapeted the card registration policy
     *
     * @var boolean
     */
    public $card_registration_policy;

    const DONATION_TYPE_RECURRING = 'recurring';
    const DONATION_TYPE_ONE_TIME = 'one-time';

    const PAYMENT_METHOD_CARD = 'card';
    const PAYMENT_METHOD_TRANSFER = 'transfer';
    const PAYMENT_METHOD_CASH = 'cash';

    public function init()
    {
        parent::init();

        if (!$this->donation_option && $this->donation_options) {
            $this->donation_option = current(array_keys($this->donation_options));
            $this->amount = current(array_keys($this->donation_options));
        } else {
            $this->amount = 5000;
        }

        // if recurring is available, and user doesnt send donation_type, then we select it
        if ($this->recurring_available && !$this->donation_type) {
            $this->donation_type = $this->default_donation_type;
        } else if (!$this->recurring_available) {
            // if recurring payment is disabled, then only one-time is acceptible
            $this->donation_type = self::DONATION_TYPE_ONE_TIME;
        }

        if (!$this->payment_method) {
            $this->payment_method = current(array_keys($this->paymentMethodList));
        }

        if ($this->donation_options && $this->custom_donation_available) {
            $this->donation_options['other'] = 'other';
        }

        if (!Yii::$app->user->isGuest) {
            $this->name = Yii::$app->user->identity->fullname;
            $this->email = Yii::$app->user->identity->email;
        }
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'email', 'payment_method', 'donation_type', 'amount'], 'required'],
            [['zip', 'city', 'street'], 'required', 'when' => function($model) {
                return $model->address_required;
            }],
            [['email'], 'email'],
            [['name', 'email', 'payment_method', 'donation_type', 'note', 'street', 'city'], 'string', 'max' => 255],
            [['message'], 'string', 'max' => 512],
            [['privacy_policy', 'card_registration_policy', 'newsletter', 'anonymous', 'zip'], 'integer'],
            [['amount'], 'integer', 'min' => 1000],
            [['amount'], 'in', 'range' => array_keys($this->donation_options), 'when' => function($model) {
                return !$model->custom_donation_available && $model->donation_options;
            }],
            [['donation_type'], 'in', 'range' => [self::DONATION_TYPE_ONE_TIME, self::DONATION_TYPE_RECURRING], 'when' => function($model) {
                return $model->recurring_available;
            }],
            [['donation_type'], 'in', 'range' => [self::DONATION_TYPE_ONE_TIME], 'when' => function($model) {
                return !$model->recurring_available;
            }],
            [['payment_method'], 'in', 'range' => [self::PAYMENT_METHOD_CARD, self::PAYMENT_METHOD_TRANSFER], 'when' => function($model) {
                return $model->donation_type != self::DONATION_TYPE_RECURRING;
            }],
            [['payment_method'], 'in', 'range' => [self::PAYMENT_METHOD_CARD], 'when' => function($model) {
                return $model->donation_type == self::DONATION_TYPE_RECURRING;
            }],
            [['privacy_policy'], 'required', 'requiredValue' => 1, 'message' => Yii::t('campaign/donation-form', 'Kérjük jelöld be, hogy elolvastad a nyilatkozatban foglaltakat')],
            [['card_registration_policy'], 'required', 'requiredValue' => 1, 'message' => Yii::t('campaign/donation-form', 'Kérjük jelöld be, hogy elolvastad a nyilatkozatban foglaltakat'), 'when' => function($model) {
                return $model->donation_type == self::DONATION_TYPE_RECURRING;
            }],
            [['donation_option'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => Yii::t('campaign/donation-form', 'Támogatás összege (forint)'),
            'donation_option' => Yii::t('campaign/donation-form', 'Támogatás összege'),
            'email' => Yii::t('campaign/donation-form', 'Email címed'),
            'name' => Yii::t('campaign/donation-form', 'Neved'),
            'message' => Yii::t('campaign/donation-form', 'Üzenet'),
            'zip' => Yii::t('campaign/donation-form', 'Irányítószám'),
            'city' => Yii::t('campaign/donation-form', 'Város'),
            'street' => Yii::t('campaign/donation-form', 'Utca / Házszám'),
            'note' => Yii::t('campaign/donation-form', 'Egyéb megjegyzés'),
            'anonymous' => Yii::t('campaign/donation-form', 'Ne jelenjen meg a nevem a támogatók között'),
            'privacy_policy' => Yii::t('campaign/donation-form', 'Az adatkezelési és továbbítási nyilatkozatot elfogadom'),
            'card_registration_policy' => Yii::t('campaign/donation-form', 'Elfogadom a kártyaregisztrációs nyilatkozatot'),
            'newsletter' => Yii::t('campaign/donation-form', 'Időnként szeretnék hírt kapni arról, hogy mihez kezdtek az adománnyal'),
        ];
    }

    public function getDonationTypeList()
    {
        $types = [
            self::DONATION_TYPE_ONE_TIME => Yii::t('campaign/donation-form', 'Egyszeri támogatás'),
            self::DONATION_TYPE_RECURRING => Yii::t('campaign/donation-form', 'Rendszeres támogatás'),
        ];

        return $types;
    }

    public function getPaymentMethodList()
    {
        return [
            self::PAYMENT_METHOD_CARD => Yii::t('campaign/donation-form', 'Kártyás fizetés'),
            self::PAYMENT_METHOD_TRANSFER => Yii::t('campaign/donation-form', 'Átutalás'),
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $hash = Yii::$app->security->generateRandomString();

        // new donation
        $model = new Donation([
            'hash' => $hash,
            'user_id' => !Yii::$app->user->isGuest ? Yii::$app->user->id : null,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'name' => Html::encode($this->name),
            'anonymous' => $this->anonymous,
            'email' => $this->email,
            'message' => Html::encode($this->message),
            'newsletter' => $this->newsletter,
            'campaign_id' => $this->campaign_id,
            'zip' => $this->zip,
            'city' => Html::encode($this->city),
            'street' => Html::encode($this->street),
            'note' => Html::encode($this->note),
            'campaign_ambassador_id' => $this->ambassador_id,
            'status' => ($this->payment_method == self::PAYMENT_METHOD_TRANSFER ? Donation::STATUS_TRANSFER_ADDED : Donation::STATUS_READY),
            'recurring' => (int) ($this->donation_type == self::DONATION_TYPE_RECURRING),
            ]);

        if ($this->newsletter && Yii::$app->params['mailchimp']['key'] !== '') {
            $this->subscribeToMailchimp();
        }

        $this->hash = $hash;

        return $model->save();
    }

    private function subscribeToMailchimp()
    {
        $mailChimp  = new MailChimp(Yii::$app->params['mailchimp']['key']);

        $names = explode(' ', $this->name);

        // feliratkozunk
        $result = $mailChimp->post('lists/' . Yii::$app->params['mailchimp']['list_id'] . '/members', [
            'email_address' => $this->email,
            'status'        => 'subscribed',
            'merge_fields'  => [
                'FNAME' => $names[0] ?? '',
                'LNAME' => $names[1] ?? '',
            ]
        ]);

        // debug-ra szedd le a kommentet az alabbi sorokrol
        // $result = $mailChimp->get('lists');
        // print_r($result);die;
        //
        return true;
    }

}
