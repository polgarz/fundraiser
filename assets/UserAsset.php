<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * Felhasznaloi oldal assetek
 */
class UserAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'dist/css/user.css',
        'https://use.fontawesome.com/releases/v5.3.1/css/all.css',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
