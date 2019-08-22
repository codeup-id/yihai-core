<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */


return \yii\helpers\ArrayHelper::merge(require __DIR__ . '/common.php', [
    'bootstrap' => ['yihai\core\Bootstrap'],
    'components' => [
//        'errorHandler' => [
//            'errorAction' => 'system/error',
//        ],
        'user' => [
            'class' => 'yihai\core\web\User',
            'identityClass' => 'yihai\core\base\UserIdent',
            'enableAutoLogin' => true,
            'groupClass' => [
                'system' => 'yihai\core\models\SysUsersSystem'
            ]
        ],
        'dashboardWidget' => [
            'class' => 'yihai\core\base\DashboardWidget'
        ] ,
        'assetManager' => [
            'linkAssets' => true,
        ],
        'view' => [
            'class' => 'yihai\core\web\View',
            'theme' => [
                'class' => '\yii\base\Theme',
                'basePath' => '@yihai-core/themes/defyihai/views',
                'baseUrl' => '@web/themes/defyihai',
                'pathMap' => [
                    '@yihai/views/layouts' => '@yihai-core/themes/defyihai/views/layouts',
                ],
            ],
        ],
        'theme' => [
            'class' => '\yihai\core\base\Theme',
            'list' => [
                '\yihai\core\themes\defyihai\Theme',
            ],
            'active' => 'defyihai',
        ],
        'notification' => [
            'class' => '\yihai\core\web\Notification'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ]
]);