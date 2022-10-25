<?php

if (YII_ENV === 'dev') {
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=127.0.0.1;dbname=fundraiser',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8mb4',
    ];
}

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=',
    'username' => '',
    'password' => '',
    'charset' => 'utf8mb4',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
