<?php

use himiklab\sitemap\behaviors\SitemapBehavior;
use yii\web\UrlNormalizer;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$rules = require __DIR__ . '/rules.php';
$simplePay = require __DIR__ . '/simplepay.php';

$clients = [];

if ($_ENV['GOOGLE_LOGIN_CLIENT_ID'] !== '') {
    $clients['google'] = [
        'class' => 'yii\authclient\clients\Google',
        'clientId' => $_ENV['GOOGLE_LOGIN_CLIENT_ID'],
        'clientSecret' => $_ENV['GOOGLE_LOGIN_CLIENT_SECRET'],
    ];
}

if ($_ENV['FACEBOOK_LOGIN_CLIENT_ID'] !== '') {
    $clients['facebook'] = [
        'class' => 'yii\authclient\clients\Facebook',
        'clientId' => $_ENV['FACEBOOK_LOGIN_CLIENT_ID'],
        'clientSecret' => $_ENV['FACEBOOK_LOGIN_CLIENT_SECRET'],
    ];
}


$config = [
    'id' => 'basic',
    'name' => $_ENV['APP_NAME'],
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'languagepicker'],
    'sourceLanguage' => 'hu-HU',
    'language' => 'hu-HU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // ezzel (es a gyokerben levo .htaccess-el) tuntetjuk el a /web/ -t az urlbol
            'baseUrl' => str_replace('/web', '', (new \yii\web\Request)->getBaseUrl()),
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '168nfkvcfNS7CKh0iXxrO8uouu3UCcfA',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                'yii\bootstrap4\BootstrapAsset' => [
                    'sourcePath' => null,
                    'js' => [],
                    'css' => [],
                    'depends' => [],
                ],
                'yii\bootstrap4\BootstrapPluginAsset' => [
                    'sourcePath' => null,
                    'js' => [],
                    'css' => [],
                    'depends' => [],
                ],
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => $clients,
        ],
        'formatter' => [
            'currencyCode' => 'HUF',
            'defaultTimeZone' => 'Europe/Budapest',
            'timeZone' => 'Europe/Budapest',
            'numberFormatterOptions' => [
                NumberFormatter::MAX_FRACTION_DIGITS => 0,
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'simplePayV2' => $simplePay,
        'user' => [
            'identityClass' => 'app\models\user\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/login'],
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['admin', 'editor'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'reCaptcha3' => [
            'class'      => 'kekaadrenalin\recaptcha3\ReCaptcha',
            'site_key'   => $_ENV['RECAPTCHA_SITE_KEY'],
            'secret_key' => $_ENV['RECAPTCHA_SECRET_KEY'],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:400',
                        'yii\i18n\PhpMessageSource',
                        'yii\i18n\PhpMessageSource::loadFallbackMessages',
                    ],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => [
                        'payment',
                    ],
                ],
            ],
        ],
        'db' => $db,
        'languagepicker' => [
            'class' => 'lajax\languagepicker\Component',
            'languages' => [
                'hu-HU' => 'HU',
                'en-GB' => 'EN',
                'de-DE' => 'DE',
            ]
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $rules,
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => UrlNormalizer::ACTION_REDIRECT_PERMANENT,
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\GettextMessageSource',
                    'sourceLanguage' => 'hu-HU',
                    'useMoFile' => false,
                ],
            ],
        ],
    ],
    'modules' => [
        'sitemap' => [
            'class' => 'himiklab\sitemap\Sitemap',
            'models' => [
                'app\models\campaign\Campaign',
                'app\models\campaign\CampaignAmbassador',
            ],
            'urls' => [
                [
                    'loc' => ['/'],
                    'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8,
                ],
                [
                    'loc' => ['/apply/index'],
                    'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8,
                ],
            ]
        ]
    ],
    'params' => $params,
];

if (YII_ENV === 'dev') {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*.*.*.*'],
        'panels' => [
            'httpclient' => [
                'class' => 'yii\\httpclient\\debug\\HttpClientPanel',
            ],
        ],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*.*.*.*'],
    ];
}

return $config;
