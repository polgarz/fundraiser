<?php

namespace app\models\campaign;

use app\models\donation\Donation;
use app\models\tag\Tag;
use Yii;
use app\models\user\User;
use himiklab\sitemap\behaviors\SitemapBehavior;
use Imagine\Image\ManipulatorInterface;
use mohorev\file\UploadImageBehavior;
use sjaakp\taggable\TaggableBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "campaign".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $status
 * @property string|null $lead
 * @property string|null $content
 * @property int|null $archive
 * @property int|null $goal
 * @property string|null $meta_description
 * @property string|null $og_image
 * @property string|null $og_description
 * @property int|null $recurring_available
 * @property string|null $default_donation_type
 * @property int|null $custom_donation_available
 * @property int|null $highlighted
 * @property string|null $highlight_image
 * @property string|null $cover_image
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property CampaignDonationOption[] $donationOptions
 * @property CampaignTag[] $campaignTags
 */
class Campaign extends \yii\db\ActiveRecord
{
    const STATUS_PUBLIC = 'public';
    const STATUS_DRAFT = 'draft';

    const DONATION_TYPE_RECURRING = 'recurring';
    const DONATION_TYPE_ONE_TIME = 'one-time';

    public $delete_og_image;
    public $delete_cover_image;
    public $delete_highlight_image;

    public $donation_options;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campaign';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            ['class' => TimestampBehavior::class, 'value' => new \yii\db\Expression('NOW()')],
            'og' => [
                'class' => UploadImageBehavior::class,
                'attribute' => 'og_image',
                'scenarios' => ['default'],
                'path' => '@webroot/uploads/images/campaign/{id}',
                'url' => '@web/uploads/images/campaign/{id}',
                'createThumbsOnRequest' => true,
                'thumbs' => [
                    'admin'  => ['width' => 445, 'height' => 250, 'mode' => ManipulatorInterface::THUMBNAIL_OUTBOUND],
                ],
            ],
            'highlight' => [
                'class' => UploadImageBehavior::class,
                'attribute' => 'highlight_image',
                'scenarios' => ['default'],
                'path' => '@webroot/uploads/images/campaign/{id}',
                'url' => '@web/uploads/images/campaign/{id}',
                'createThumbsOnRequest' => true,
                'thumbs' => [
                    'admin'  => ['width' => 445, 'height' => 250, 'mode' => ManipulatorInterface::THUMBNAIL_OUTBOUND],
                    'main'  => ['width' => 1100, 'height' => 275, 'mode' => ManipulatorInterface::THUMBNAIL_OUTBOUND],
                ],
            ],
            'cover' => [
                'class' => UploadImageBehavior::class,
                'attribute' => 'cover_image',
                'scenarios' => ['default'],
                'path' => '@webroot/uploads/images/campaign/{id}',
                'url' => '@web/uploads/images/campaign/{id}',
                'createThumbsOnRequest' => true,
                'thumbs' => [
                    'admin'  => ['width' => 445, 'height' => 250, 'mode' => ManipulatorInterface::THUMBNAIL_OUTBOUND],
                    'list'  => ['width' => 1100, 'height' => 275, 'mode' => ManipulatorInterface::THUMBNAIL_OUTBOUND],
                ],
            ],
            [
                'class' => TaggableBehavior::class,
                'junctionTable' => 'campaign_tag',
                'tagClass' => Tag::class,
                'nameAttribute' => 'tag',
                'tagKeyColumn' => 'tag_id',
                'modelKeyColumn' => 'campaign_id',
                'orderKeyColumn' => 'order',
            ],
            'sitemap' => [
                'class' => SitemapBehavior::class,
                'scope' => function ($model) {
                    /** @var \yii\db\ActiveQuery $model */
                    $model->select(['slug', 'updated_at']);
                    $model->andWhere(['status' => self::STATUS_PUBLIC]);
                },
                'dataClosure' => function ($model) {
                    /** @var self $model */
                    return [
                        'loc' => Url::to(['/campaign/details', 'slug' => $model->slug], true),
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
    public function rules()
    {
        return [
            [['title', 'slug'], 'required'],
            [['status', 'lead', 'content', 'default_donation_type'], 'string'],
            [['archive', 'recurring_available', 'custom_donation_available', 'highlighted', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'slug', 'meta_description', 'og_description', 'highlight_subtitle'], 'string', 'max' => 255],
            [['highlight_text'], 'string', 'max' => 512],
            [['slug'], 'unique'],
            [['goal'], 'integer', 'min' => 0],
            [['og_image', 'cover_image', 'highlight_image'], 'image', 'extensions' => 'jpg, jpeg, gif, png'],
            [['status'], 'in', 'range' => array_keys($this->statusList)],
            [['default_donation_type'], 'in', 'range' => array_keys($this->donationTypeList)],
            [['delete_cover_image', 'delete_og_image', 'delete_highlight_image', 'ambassador_can_apply', 'address_required'], 'boolean'],
            [['donation_options'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    public function getStatusList()
    {
        return [
            self::STATUS_DRAFT => 'Vázlat',
            self::STATUS_PUBLIC => 'Publikus',
        ];
    }

    public function getDonationTypeList()
    {
        return [
            self::DONATION_TYPE_ONE_TIME => 'Egyszeri',
            self::DONATION_TYPE_RECURRING => 'Rendszeres',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Cím',
            'slug' => 'Keresőbarát URL',
            'status' => 'Státusz',
            'lead' => 'Bevezető',
            'tags' => 'Cimkék',
            'content' => 'Tartalom',
            'archive' => 'Archív',
            'goal' => 'Célösszeg',
            'meta_description' => 'Meta leírás',
            'og_image' => 'Facebook kép',
            'cover_image' => 'Borítókép',
            'address_required' => 'Kötelező a megadni a címet',
            'og_description' => 'Facebook leírás',
            'recurring_available' => 'Rendszeres fizetés elérhető',
            'default_donation_type' => 'Alapértelmezett támogatási rendszeresség',
            'custom_donation_available' => 'Egyedi összeg is megadható',
            'highlighted' => 'Legyen kiemelt kampány',
            'highlight_image' => 'Kép',
            'highlight_subtitle' => 'Alcím',
            'highlight_text' => 'Szöveg',
            'created_at' => 'Létrehozva',
            'updated_at' => 'Módosítás dátuma',
            'created_by' => 'Létrehozta',
            'updated_by' => 'Utoljára módosította',
            'donation_options' => 'Adományozási összegek',
            'ambassador_can_apply' => 'Jelentkezhetnek nagykövetek',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->delete_og_image) {
            $this->og_image = null;
        }

        if ($this->delete_cover_image) {
            $this->cover_image = null;
        }

        if ($this->delete_highlight_image) {
            $this->highlight_image = null;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->tags) {
            $this->tags = implode(',', $this->tags);
        }

        if ($this->donation_options) {
            CampaignDonationOption::deleteAll(['campaign_id' => $this->id]);
            foreach ($this->donation_options as $option) {
                $model = new CampaignDonationOption([
                    'campaign_id' => $this->id,
                    'name' => $option['name'],
                    'value' => $option['value'],
                    'order' => $option['order'],
                ]);

                if ($model->validate()) {
                    $this->link('donationOptions', $model);
                }
            }
        } else {
            CampaignDonationOption::deleteAll(['campaign_id' => $this->id]);
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();

        if ($this->tags) {
            $this->tags = explode(',', $this->tags);
        }

        foreach($this->donationOptions as $option) {
            $this->donation_options[] = \yii\helpers\ArrayHelper::toArray($option);
        }
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[CampaignDonationOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDonationOptions()
    {
        return $this->hasMany(CampaignDonationOption::class, ['campaign_id' => 'id']);
    }

    /**
     * Gets query for [[CampaignAmbassador]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAmbassadors()
    {
        return $this->hasMany(CampaignAmbassador::class, ['campaign_id' => 'id']);
    }

    /**
     * Gets query for [[CampaignTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCampaignTags()
    {
        return $this->hasMany(CampaignTag::class, ['campaign_id' => 'id']);
    }

    public function getRenderedContent()
    {
        if (empty($this->content)) {
            return $this->content;
        }

        try {
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);

            $dom->loadHTML('<html>' . mb_convert_encoding($this->content, 'HTML-ENTITIES', 'UTF-8') . '</html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            // ha van webp a kepbol, akkor csereljuk
            $imgs = $dom->getElementsByTagName('img');
            foreach ($imgs as $img) {
                $original_path = Yii::getAlias('@app/web' . $img->getAttribute('src'));
                // ha ez a file nalunk van
                if (is_file($original_path)) {
                    $pathinfo = pathinfo($img->getAttribute('src'));
                    $webp_path = Yii::getAlias('@app/web' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.webp');

                    // es van webp verzio is
                    if (is_file($webp_path)) {
                        // csereljuk a taget picturre
                        $webp_source = $dom->createElement('source');
                        $webp_source->setAttribute('srcset', $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.webp');
                        $webp_source->setAttribute('type', mime_content_type($webp_path));

                        $orig_source = $dom->createElement('source');
                        $orig_source->setAttribute('srcset', $img->getAttribute('src'));
                        $orig_source->setAttribute('type', mime_content_type($original_path));

                        $new_img = clone $img;

                        $picture = $dom->createElement('picture');
                        $picture->appendChild($webp_source);
                        $picture->appendChild($orig_source);
                        $picture->appendChild($new_img);

                        $img->parentNode->replaceChild($picture, $img);
                    }
                }
            }

            return str_replace(array('<html>','</html>') , '' , $dom->saveHTML());
        } catch(\Exception $e) {
            Yii::error($e->getMessage());
            return $this->content;
        }
    }

    /**
     * Donations
     *
     * @return array
     */
    public function getDonations()
    {
        return Donation::find()
            ->with('ambassador')
            ->where(['campaign_id' => $this->id])
            ->andWhere(['status' => Donation::STATUS_FINISHED])
            ->andWhere(['is', 'parent_id', null])
            ->orderBy('created_at DESC')
            ->all();
    }

    public function getCollected()
    {
        return (int) Donation::find()
            ->where(['campaign_id' => $this->id])
            ->andWhere(['status' => Donation::STATUS_FINISHED])
            ->andWhere(['is', 'parent_id', null])
            ->sum('amount');
    }
}
