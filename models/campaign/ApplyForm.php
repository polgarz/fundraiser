<?php

namespace app\models\campaign;

use Yii;
use yii\base\Model;
use yii\helpers\Inflector;
use yii\helpers\Html;
use yii\web\UploadedFile;

/**
 * Apply form (as ambassador)
 */
class ApplyForm extends Model
{
    public $name;
    public $lead;
    public $image;
    public $goal;
    public $content;
    public $privacy;
    public $address;

    /**
     * @var \common\models\campaign\Campaign
     */
    private $_campaign;

    /**
     * Creates a form model given a token.
     *
     * @param Campaign $campaign
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($campaign, $config = [])
    {
        $this->_campaign = $campaign;

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['lead', 'goal', 'content', 'name'], 'required'],
            [['lead'], 'string', 'max' => 140],
            [['name'], 'string', 'max' => 35],
            [['name'], function($attribute, $params) {
                if (CampaignAmbassador::findOne(['campaign_id' => $this->_campaign->id, 'slug' => Inflector::slug($this->$attribute)])) {
                    $this->addError($attribute, Yii::t('campaign/ambassador', 'Ez a kampány név már foglalt, kérjük válassz másikat!'));
                }
            }],
            [['content'], 'string'],
            [['goal'], 'integer', 'min' => 6000],
            [['image'], 'image', 'extensions' => 'jpg, jpeg, gif, png'],
            [['privacy'], 'boolean'],
            [['privacy'], 'required', 'requiredValue' => 1, 'message' => Yii::t('campaign/ambassador', 'Kérjük fogadd el az adatvédelmi tájékoztatóban foglaltakat')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('campaign/ambassador', 'Kampányod címe'),
            'lead' => Yii::t('campaign/ambassador', 'Kampányod rövid leírása'),
            'image' => Yii::t('campaign/ambassador', 'Kép a kampányodhoz'),
            'goal' => Yii::t('campaign/ambassador', 'Célösszeg'),
            'content' => Yii::t('campaign/ambassador', 'Kampányod teljes szövege'),
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $model = new CampaignAmbassador([
            'campaign_id' => $this->_campaign->id,
            'lead' => Html::encode($this->lead),
            'name' => Html::encode($this->name),
            'content' => Html::encode($this->content),
            'goal' => Html::encode($this->goal),
            'user_id' => Yii::$app->user->id,
            'slug' => Inflector::slug($this->name),
            'image' => UploadedFile::getInstance($this, 'image'),
            'approved' => 0,
            ]);

        return $model->save();
    }
}
