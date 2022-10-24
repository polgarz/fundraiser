<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'dist/js/general.min.js'
    ];
    public $css = [
        'dist/css/campaign.css',
    ];
}
