<?php

namespace app\models\donation;

use Yii;
use app\models\campaign\CampaignAmbassador;
use app\models\campaign\Campaign;
use app\models\user\User;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "donation".
 *
 * @property int $id
 * @property string $hash
 * @property int|null $user_id
 * @property string $payment_method
 * @property int|null $amount
 * @property string $name
 * @property int|null $anonymous
 * @property string $email
 * @property string|null $message
 * @property int|null $newsletter
 * @property int|null $campaign_id
 * @property int|null $campaign_ambassador_id
 * @property string $status
 * @property int|null $vendor_ref
 * @property int|null $recurring
 * @property int|null $parent_id
 * @property string|null $token
 * @property string|null $token_due_date
 * @property string|null $created_at
 *
 * @property CampaignAmbassador $campaignAmbassador
 * @property Campaign $campaign
 * @property Donation $parent
 * @property Donation[] $donations
 * @property User $user
 */
class Donation extends \yii\db\ActiveRecord
{
    const STATUS_TRANSFER_ADDED = 'transfer-added';
    const STATUS_READY = 'ready';
    const STATUS_ERROR = 'error';
    const STATUS_TIMEOUT = 'timeout';
    const STATUS_CANCELED = 'canceled';
    const STATUS_SUCCESS = 'success';
    const STATUS_UNKNOWN = 'unknown';
    const STATUS_EXPIRED = 'expired';

    // displayable
    const STATUS_FINISHED = 'finished';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'donation';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::class, 'value' => new \yii\db\Expression('NOW()'), 'updatedAtAttribute' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hash', 'payment_method', 'name', 'email', 'status'], 'required'],
            [['user_id', 'amount', 'anonymous', 'newsletter', 'campaign_id', 'campaign_ambassador_id', 'vendor_ref', 'recurring', 'parent_id'], 'integer'],
            [['payment_method'], 'string'],
            [['token_due_date', 'created_at'], 'safe'],
            [['hash', 'name', 'email', 'status', 'token'], 'string', 'max' => 255],
            [['message'], 'string', 'max' => 512],
            [['hash'], 'unique'],
            [['campaign_ambassador_id'], 'exist', 'skipOnError' => true, 'targetClass' => CampaignAmbassador::class, 'targetAttribute' => ['campaign_ambassador_id' => 'id']],
            [['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => Campaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Donation::class, 'targetAttribute' => ['parent_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Azon.',
            'hash' => 'Hash',
            'user_id' => 'Felhasználó',
            'payment_method' => 'Fizetési mód',
            'amount' => 'Összeg',
            'name' => 'Név',
            'anonymous' => 'Anonim',
            'email' => 'Email',
            'message' => 'Üzenet',
            'newsletter' => 'Hírlevél',
            'campaign_id' => 'Kampány',
            'campaign_ambassador_id' => 'Nagykövet',
            'status' => 'Státusz',
            'vendor_ref' => 'SimplePay azonosító',
            'recurring' => 'Rendszeres',
            'parent_id' => 'Szülő adomány',
            'token' => 'Token (technikai)',
            'token_due_date' => 'Token beváltási ideje (technikai)',
            'created_at' => 'Dátum',
            'zip' => 'Irányítószám',
            'city' => 'Város',
            'street' => 'Utca / házszám',
            'note' => 'Egyéb megyjezés a címhez',
        ];
    }

    public function getStatusList()
    {
        return [
            self::STATUS_TRANSFER_ADDED => 'Függőben',
            self::STATUS_READY => 'Elkezdve',
            self::STATUS_ERROR => 'Hiba',
            self::STATUS_TIMEOUT => 'Időtúllépés',
            self::STATUS_CANCELED => 'Megszakítva',
            self::STATUS_SUCCESS => 'Sikeres, fűggő',
            self::STATUS_UNKNOWN => 'Ismeretlen',
            self::STATUS_FINISHED => 'Sikeres, jóváhagyott',
            self::STATUS_EXPIRED => 'Lejárt',
        ];
    }

    public function getPaymentMethodList()
    {
        return [
            DonationForm::PAYMENT_METHOD_CARD => 'Bankkártya',
            DonationForm::PAYMENT_METHOD_TRANSFER => 'Utalás',
            DonationForm::PAYMENT_METHOD_CASH => 'Készpénz',
        ];
    }

    /**
     * Gets query for [[CampaignAmbassador]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAmbassador()
    {
        return $this->hasOne(CampaignAmbassador::class, ['id' => 'campaign_ambassador_id']);
    }

    /**
     * Gets query for [[Campaign]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCampaign()
    {
        return $this->hasOne(Campaign::class, ['id' => 'campaign_id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Donation::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Donations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDonations()
    {
        return $this->hasMany(Donation::class, ['parent_id' => 'id']);
    }

    /**
     * GTM DataLayer
     *
     * @return array
     */
    public function getDataLayer(): array
    {
        return [
            'event' => 'transaction',
            'ecommerce' => [
                'currencyCode' => 'HUF',
                'purchase' => [
                    'actionField' => [
                        'id' => $this->id,
                        'affiliation' => Yii::$app->name,
                        'revenue' => $this->amount,
                        'tax' => 0,
                        'shipping' => 0,
                    ],
                    'products' => [
                        [
                            'name' => 'Adomány',
                            'id' => $this->id,
                            'price' => $this->amount,
                            'quantity' => 1,
                        ]
                    ],
                ],
            ]
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getHasActiveTokens()
    {
        if ($this->donations) {
            foreach($this->donations as $donation) {
                if ($this->status == self::STATUS_FINISHED && $donation->token && $donation->status == self::STATUS_READY) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getRecurringSequenceNumber()
    {
        if ($this->recurring) {
            if ($this->parent_id) {

                $donations = $this->parent->donations;

                uasort($donations, function($a, $b) {
                    if ($a->token_due_date == $b->token_due_date) {
                        return 0;
                    }
                    return ($a->token_due_date < $b->token_due_date) ? -1 : 1;
                });

                $i = 2;
                foreach($donations as $donation) {
                    if ($donation->id == $this->id) {
                        return $i;
                    }
                    $i++;
                }

            } else {
                return 1;
            }
        }

        return null;
    }
}
