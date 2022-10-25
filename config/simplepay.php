<?php

return [
    'class' => 'znagy\SimplePayV2\SimplePayV2',
    'sdkConfig' => [
        'HUF_MERCHANT' => $_ENV['SIMPLEPAY_MERCHANT_ID'],
        'HUF_SECRET_KEY' => $_ENV['SIMPLEPAY_SECRET_KEY'],
        'SANDBOX' => YII_ENV === 'dev',
        'URL' => ['donate/status'],
        'LOGGER' => true,
        'LOG_PATH' => dirname(__DIR__) . '/logs',
    ],
    'defaultCurrency' => 'HUF',
    'defaultLanguage' => 'HU',
    'defaultPaymentMethod' => 'CARD',
    'defaultTimeoutInSec' => '600',
];
