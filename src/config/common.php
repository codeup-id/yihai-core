<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */


return [
    'bootstrap' => ['yihai\core\Bootstrap'],
    'layoutPath' => '@yihai/views/_layouts',
    'params' => require __DIR__ . '/params.php',
    'components' => [
        'authManager' => [
            'class' => 'yihai\core\rbac\PhpManager',
        ],
        'formatter' => [
            'class' => 'yihai\core\i18n\Formatter',
//            'datetimeFormat' => 'php:Y-m-d H:i:s'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
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
        'i18n' => [
            'translations' => [
                'yihai*' => [
                    'class' => 'yihai\core\i18n\PhpMessageSource',
                    'basePath' => '@yihai/messages',
                    'fileMap' => [
                    ]
                ]
            ]
        ],
        'settings' => [
            'class' => 'yihai\core\base\Settings'
        ],
        'reports' => [
            'class' => 'yihai\core\report\ReportComponent'
        ],
    ]
];
