<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name'=>'shop',
    'language'=>'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'leonid',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser',

            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        /*Добавьте этот компонент для формирования ответа
        здесь формируется ответ если пользователь неавторизован
        см. Методические указания стр. 21*/
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null && $response->statusCode==401) {
                    $response->data = ['error'=>['code'=>403, 'message'=>'Unauthorized']];
                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                }
            },
        ],

        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
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
        'db' => $db,


        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,

            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'trip'],// и так далее все табл.
                ['class' => 'yii\rest\UrlRule', 'controller' => 'ticket'],

                'POST register' => 'user/create',//+
                'POST login' => 'user/login',//+
                'GET user' => 'user/account',//+
                'GET users' => 'user/users',//+
              //  'PATCH user/red' => 'user/red',
                'DELETE user/del' =>'user/del',//+

                'GET trip' => 'trip/trip',//+
                'POST find' => 'trip/find',//+
                'GET alltrip' => 'trip/alltrip',//+
                'GET ticket' => 'ticket/show',//
                'POST order' => 'ticket/order',//+
                'POST trip/add' => 'trip/add',//+
                'PATCH trip/red/<id_trip>' => 'trip/red',//+
                'DELETE trip/del/<id_trip>' => 'trip/del',//+
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];
}

return $config;