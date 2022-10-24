<?php

namespace app\models\tag;

use Yii;
use app\models\post\PostTag;
use app\models\post\PostCategoryTag;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $tag
 *
 * @property PostCategoryTag[] $postCategoryTags
 * @property PostTag[] $postTags
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tag'], 'required'],
            [['tag'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag' => 'Tag',
        ];
    }

    /**
     * Gets query for [[PostCategoryTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostCategoryTags()
    {
        return $this->hasMany(PostCategoryTag::class, ['tag_id' => 'id']);
    }

    /**
     * Gets query for [[PostTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostTags()
    {
        return $this->hasMany(PostTag::class, ['tag_id' => 'id']);
    }
}
