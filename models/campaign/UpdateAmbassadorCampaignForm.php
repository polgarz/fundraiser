<?php

namespace app\models\campaign;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\web\UploadedFile;

/**
 * Update ambassador campaign form (as ambassador)
 */
class UpdateAmbassadorCampaignForm extends Model
{
    public $lead;
    public $image;
    public $goal;
    public $content;
    public $name;

    /**
     * @var \common\models\campaign\CampaignAmbassador
     */
    private $_campaignAmbassador;

    /**
     * Creates a form model given a token.
     *
     * @param CampaignAmbassador $campaignAmbassador
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($campaignAmbassador, $config = [])
    {
        $this->_campaignAmbassador = $campaignAmbassador;

        $this->setAttributes($campaignAmbassador->getAttributes());

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['lead', 'goal', 'content', 'name'], 'required'],
            [['lead'], 'string', 'max' => 140],
            [['name'], 'string', 'max' => 35],
            [['content'], 'string'],
            [['goal'], 'integer', 'min' => 10000],
            [['image'], 'image', 'extensions' => 'jpg, jpeg, gif, png'],
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

        $this->_campaignAmbassador->setAttributes([
            'lead' => Html::encode($this->lead),
            'content' => Html::encode($this->content),
            'goal' => Html::encode($this->goal),
            'name' => Html::encode($this->name),
            'image' => UploadedFile::getInstance($this, 'image'),
            ]);

        return $this->_campaignAmbassador->save();
    }
}