<?php

namespace app\models\campaign;

use Yii;

/**
 * This is the model class for table "campaign_donate_option".
 *
 * @property int $id
 * @property int $campaign_id
 * @property string $name
 * @property int $value
 * @property int|null $order
 *
 * @property Campaign $campaign
 */
class CampaignDonationOption extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campaign_donation_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['campaign_id', 'name', 'value'], 'required'],
            [['campaign_id', 'value', 'order'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['value'], 'integer', 'min' => 0],
            [['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => Campaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'campaign_id' => 'Campaign ID',
            'name' => 'Név',
            'value' => 'Összeg',
            'order' => 'Sorrend',
        ];
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
}
