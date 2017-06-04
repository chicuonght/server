<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'authManager' => [
            'class' => 'auth\rbac\DbManager',
            'defaultRoles' => ['user']
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
