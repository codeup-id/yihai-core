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
        'fileManager' => [
            'class' => 'yihai\core\extension\FileManager',
            'connectOptions' => [
            ],
            'volumes' => [
                [
                    'class' => 'yihai\core\extension\elfinder\volume\Local',
                    'id' => 'root',
                    'position' => 0,
                    'path' => '.',
                    'name' => 'ROOT (Superuser)',
                    'plugin' => [
                        'Sluggable' => [

                        ]
                    ],
                    'access_read' => [\yihai\core\rbac\RbacHelper::roleRoleName('superuser')],
                    'access_write' => [\yihai\core\rbac\RbacHelper::roleRoleName('superuser')],
                ],
                [
                    'class' => 'yihai\core\extension\elfinder\volume\UserPath',
                    'id' => 'home-user',
                    'position' => 0,
                    'path' => 'users/{id}',
                    'name' => 'Home',
                    'plugin' => [
                        'Sluggable' => [

                        ]
                    ],
//                    'access_read' => [],
//                    'access_write' => [],
//                    'watermark' => [
//                        'source'         => '/Users/upik/www/codeup/yihai-cms/cms/web/assets/49572765/default_avatar.png', // Path to Water mark image
//                        'marginRight'    => 5,          // Margin right pixel
//                        'marginBottom'   => 5,          // Margin bottom pixel
//                        'quality'        => 95,         // JPEG image save quality
//                        'transparency'   => 70,         // Water mark image transparency ( other than PNG )
//                        'targetType'     => IMG_GIF|IMG_JPG|IMG_PNG|IMG_WBMP, // Target image formats ( bit-field )
//                        'targetMinPixel' => 200         // Target image minimum pixel size
//
//                    ]
                ],
            ],
        ]
    ]
]);