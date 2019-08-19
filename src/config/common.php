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
];
