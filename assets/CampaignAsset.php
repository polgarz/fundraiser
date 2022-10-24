<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * Kampany oldal assetek
 */
class CampaignAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'dist/css/campaign.css',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
