<?php

namespace app\models\campaign;

use Yii;
use app\models\tag\Tag;

/**
 * This is the model class for table "campaign_tag".
 *
 * @property int $id
 * @property int|null $campaign_id
 * @property int|null $tag_id
 * @property int|null $order
 *
 * @property Campaign $campaign
 * @property Tag $tag
 */
class CampaignTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campaign_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['campaign_id', 'tag_id', 'order'], 'integer'],
            [['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => Campaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tag_id' => 'id']],
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
            'tag_id' => 'Tag ID',
            'order' => 'Order',
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

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id']);
    }
}
