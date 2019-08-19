<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */


return \yii\helpers\ArrayHelper::merge(require __DIR__.'/common.php', [
    'bootstrap' => ['yihai\core\Bootstrap'],
    'controllerMap' => [
        'setup' => 'yihai\core\console\controllers\SetupController',
        'modules' => 'yihai\core\console\controllers\ModulesController',
        'security' => 'yihai\core\console\controllers\SecurityController',
        'migrate' => 'yihai\core\console\controllers\MigrateController',
    ],
    'components' => [

        'i18n' => [
            'translations' => [
                'yihai*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yihai/messages',
                    'fileMap' => [
                        'yihai' => '@yihai-core/messages/en/yihai.php'
                    ]
                ]
            ]
        ],
    ]
]);