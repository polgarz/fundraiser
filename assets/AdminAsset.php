<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Admin application asset bundle.
 */
class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'dist/css/admin.css',
        'https://use.fontawesome.com/releases/v5.3.1/css/all.css',
    ];
    public $js = [
        'https://cdn.ckeditor.com/ckeditor5/35.2.1/classic/ckeditor.js',
        'https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js',
        '/dist/js/admin.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
