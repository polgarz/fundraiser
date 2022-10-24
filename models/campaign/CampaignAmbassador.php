<?php

namespace app\models\campaign;

use app\models\donation\Donation;
use Yii;
use app\models\user\User;
use himiklab\sitemap\behaviors\SitemapBehavior;
use Imagine\Image\ManipulatorInterface;
use mohorev\file\UploadImageBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;
use yii\imagine\Image;

/**
 * This is the model class for table "campaign_ambassador".
 *
 * @property int $id
 * @property int $campaign_id
 * @property int|null $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $lead
 * @property string|null $image
 * @property int|null $goal
 * @property string|null $content
 * @property string|null $commitment
 * @property int|null $approved
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Campaign $campaign
 * @property User $user
 */
class CampaignAmbassador extends \yii\db\ActiveRecord
{
    public $delete_image;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            ['class' => TimestampBehavior::class, 'value' => new \yii\db\Expression('NOW()')],
            [
                'class' => UploadImageBehavior::class,
                'attribute' => 'image',
                'scenarios' => ['default'],
                'path' => '@webroot/uploads/images/ambassador/{id}',
                'url' => '@web/uploads/images/ambassador/{id}',
                'createThumbsOnRequest' => true,
                'thumbs' => [
                    'icon'  => ['width' => 32, 'height' => 32, 'mode' => ManipulatorInterface::THUMBNAIL_OUTBOUND],
                    'list' => ['width' => 400, 'height' => 400, 'mode' => ManipulatorInterface::THUMBNAIL_OUTBOUND],
                ],
            ],
            'sitemap' => [
                'class' => SitemapBehavior::class,
                'scope' => function ($model) {
                    /** @var \yii\db\ActiveQuery $model */
                    $model->joinWith(['campaign']);
                    $model->andWhere(['status' => Campaign::STATUS_PUBLIC]);
                },
                'dataClosure' => function ($model) {
                    /** @var self $model */
                    return [
                        'loc' => Url::to(['/campaign/ambassador', 'campaign_slug' => $model->campaign->slug, 'slug' => $model->slug], true),
                        'lastmod' => strtotime($model->updated_at),
                        'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                        'priority' => 0.8
                    ];
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campaign_ambassador';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['campaign_id', 'slug'], 'required'],
            [['campaign_id', 'user_id', 'goal', 'created_by', 'updated_by', 'approved', 'delete_image'], 'integer'],
            [['lead', 'content', 'name', 'commitment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['slug', 'name', 'tshirt_size', 'address', 'commitment'], 'string', 'max' => 255],
            [['image'], 'image', 'extensions' => 'jpg, jpeg, png'],
            [['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => Campaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // save webp version
        $path = $this->getThumbUploadPath('image', 'list');
        if (is_file($path)) {
            $info = pathinfo($path);

            Image::getImagine()->open($path)
                ->save($info['dirname'] . '/' . $info['filename'] . '.webp');
        }

        if ($insert) {
            foreach(Yii::$app->params['campaignNotificationRecipients'] as $recipient) {
                Yii::$app->mailer->compose(['html' => 'campaign/new-ambassador-notification'], [
                    'model' => $this,
                ])->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setTo($recipient)
                ->setSubject('Új nagykövet: ' . $this->name)
                ->send();
            }
        }

        if (isset($changedAttributes['approved']) && !$changedAttributes['approved'] && $this->approved && $this->user) {
            Yii::$app->mailer->compose(['html' => 'campaign/campaign-approved-notification'], [
                'model' => $this,
            ])->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo([$this->user->email => $this->user->fullname])
            ->setSubject('Az adománygyűjtő kampányodat engedélyeztük')
            ->send();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->content = strip_tags($this->content);

        if ($this->delete_image) {
            $this->image = null;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Felhasználó',
            'slug' => 'Slug',
            'name' => 'Kampány címe',
            'lead' => 'Rövid leírás',
            'image' => 'Kép',
            'goal' => 'Célösszeg',
            'content' => 'Tartalom',
            'tshirt_size' => 'Pólóméret',
            'address' => 'Levelezési cím',
            'created_at' => 'Létrehozva',
            'updated_at' => 'Módosítás dátuma',
            'created_by' => 'Létrehozta',
            'updated_by' => 'Utoljára módosította',
            'collected' => 'Összegyűjtve',
            'approved' => 'Jóváhagyva',
            'commitment' => 'Vállalás',
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Donations
     *
     * @return array
     */
    public function getDonations()
    {
        return Donation::find()
            ->where(['campaign_ambassador_id' => $this->id])
            ->andWhere(['status' => Donation::STATUS_FINISHED])
            ->andWhere(['is', 'parent_id', null])
            ->orderBy('created_at DESC')
            ->all();
    }

    /**
     * Collected amount
     *
     * @return integer
     */
    public function getCollected() : int
    {
        return (int) Donation::find()
            ->where(['campaign_ambassador_id' => $this->id])
            ->andWhere(['status' => Donation::STATUS_FINISHED])
            ->andWhere(['is', 'parent_id', null])
            ->sum('amount');
    }
}
