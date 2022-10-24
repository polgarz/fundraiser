<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * TinyMCE asset bundle.
 */
class TinyMCEAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];

    public function init()
    {
        $this->js[] = sprintf('https://cdn.tiny.cloud/1/%s/tinymce/5/tinymce.min.js', Yii::$app->params['tinymceApiKey'] ?? 'no-api-key');
    }
}
