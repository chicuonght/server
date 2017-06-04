<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'auth',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'auth\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'authManager' => [
            'class' => 'auth\rbac\DbManager',
            'defaultRoles' => ['user']
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                // use temporary redirection instead of permanent for debugging
                'action' => \yii\web\UrlNormalizer::ACTION_REDIRECT_TEMPORARY,
            ],
            'rules' => [
                'POST authorize' => 'permission/authorize',
                'GET <controller:(permission|role)>s' => '<controller>/index',
                'GET <controller:(permission|role)>s/<userId:\d+>' => '<controller>/by-user',
                'GET <controller:(permission)>s/<roleName:\w+>' => '<controller>/by-role',
                'DELETE <controller:(role)>s/<roleName:\w+>' => '<controller>/delete',
                'POST <controller:(role)>s' => '<controller>/create',
                'PUT <controller:(role)>s' => '<controller>/update',
                'POST <controller:(role)>s/<roleName:\w+>/assign/<userId:\d+>' => '<controller>/assign',
                'DELETE <controller:(role)>s/<roleName:\w+>/assign/<userId:\d+>' => '<controller>/unassign',
                //['class' => 'yii\rest\UrlRule', 'controller' => 'role'],
            ],
        ],
        'request' => [
            'parsers' => [
                //'application/json' => 'yii\web\JsonParser',
                'application/vnd.api+json' => 'tuyakhov\jsonapi\JsonApiParser',
            ]
        ],
        'response' => [
            // ...
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'tuyakhov\jsonapi\JsonApiResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'auth\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@auth/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
