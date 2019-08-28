<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system;


use Yihai;
use yihai\core\rbac\RbacHelper;
use yihai\core\web\Menu;

class Module extends \yihai\core\base\Module
{
    public $settingsClass = ModuleSetting::class;
    public $layout = '@yihai/views/_layouts/backend';
    /**
     * @var array|\yii\web\UrlRule
     */
    public $urlRuleClass = 'yii\web\UrlRule';
    public $controllerNamespace = 'yihai\core\modules\system\controllers';


    public function init()
    {
        parent::init();
    }
    public function init_web()
    {
        parent::init_web();
        Yihai::$app->user->loginUrl = ['/system/login'];
    }

    public function addMenu()
    {
        Menu::add('', [
            'id' => 'backend',
            'label' => 'Backend',
            'position' => 10,
            'type' => Menu::TYPE_HEADER,
            'child' => [
                'system' => new Menu([
                    'id' => 'system',
                    'label' => 'System',
                    'position' => 20,
                    'type' => Menu::TYPE_HEADER,
                    'child' => [
                        'users' => new Menu([
                            'icon' => 'users',
                            'id' => 'users',
                            'type' => Menu::TYPE_GROUP,
                            'label' => Yihai::t('yihai', 'Pengguna'),
                            'position' => 10,
                            'child' => [
                                'users' => new Menu([
                                    'type' => Menu::TYPE_MENU,
                                    'label' => Yihai::t('yihai', 'Semua Pengguna'),
                                    'id' => 'users',
                                    'route' => ['/system/users/index'],
                                    'icon' => 'users',
                                ]),
                                'system' => new Menu([
                                    'type' => Menu::TYPE_MENU,
                                    'id' => 'system',
                                    'label' => 'System/Superuser',
                                    'route' => ['/system/users-system/index'],
                                    'icon' => 'users',
                                ]),
                            ]
                        ]),
                        'settings' => new Menu([
                            'type' => Menu::TYPE_GROUP,
                            'id' => 'settings',
                            'label' => Yihai::t('yihai', 'Pengaturan'),
                            'position' => 20,
                            'icon' => 'settings',
                            'child' => [
                                'roles' => new Menu([
                                    'type' => Menu::TYPE_GROUP,
                                    'id' => 'roles',
                                    'label' => 'Roles Control',
                                    'icon' => 'roles-permissions',
                                    'child' => [
                                        'roles' => new Menu([
                                            'id' => 'roles',
                                            'type' => Menu::TYPE_MENU,
                                            'label' => 'Roles',
                                            'route' => ['/system/roles/roles']
                                        ]),
                                        'permissions' => new Menu([
                                            'id' => 'roles',
                                            'type' => Menu::TYPE_MENU,
                                            'label' => 'Permissions',
                                            'route' => ['/system/roles/permissions']
                                        ]),
                                        'assign' => new Menu([
                                            'id' => 'roles',
                                            'type' => Menu::TYPE_MENU,
                                            'label' => 'Assign User',
                                            'route' => ['/system/roles/assign']
                                        ])
                                    ]
                                ]),
                                'modules' => new Menu([
                                    'type' => Menu::TYPE_GROUP,
                                    'id' => 'modules',
                                    'label' => Yihai::t('yihai', 'Modul'),
                                    'icon' => 'setting',
                                    'child' => [

                                    ]
                                ]),
                                'settings' => new Menu([
                                    'type' => Menu::TYPE_MENU,
                                    'id' => 'settings',
                                    'label' => Yihai::t('yihai', 'Semua pengaturan'),
                                    'icon' => 'setting',
                                    'route' => ['/system/settings'],
                                ])
                            ]
                        ]),
                        'report' => new Menu([
                            'type' => Menu::TYPE_MENU,
                            'id' => 'report',
                            'route' => ['/system/reports/index'],
                            'label' => Yihai::t('yihai', 'Laporan/Dokumen'),
                            'position' => 100,
                            'icon' => 'reports',
                        ]),
                        'utilities' => new Menu([
                            'type' => Menu::TYPE_GROUP,
                            'id' => 'utilities',
                            'position' => 200,
                            'child' => [
                                'log' => new Menu([
                                    'type' => Menu::TYPE_MENU,
                                    'id' => 'log',
                                    'label' => 'Activity Log',
                                    'route' => ['/system/activity-log'],
                                    'icon' => 'file',
                                    'position' => 10,
                                ]),
                                'filemanager' => new Menu([
                                    'type' => Menu::TYPE_MENU,
                                    'id' => 'filemanager',
                                    'label' => 'File Manager',
                                    'route' => ['/system/file-manager/index'],
                                    'permissions' => [RbacHelper::roleRoleName('superuser')],
                                    'icon' => 'file',
                                    'position' => 20,
                                ]),
                            ]
                        ]),

                    ]
                ])
            ]
        ]);
        parent::addMenu();
    }

    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            [
                'class' => 'yii\web\GroupUrlRule',
                'prefix' => 'system',
                'rules' => [
                    'index' => 'default/index',
                    'login' => 'default/login',
                    'profile' => 'default/profile',
                    'profile-update' => 'default/profile-update',
                    'change-password' => 'default/change-password',
                    'logout' => 'default/logout',
//                    '<controller:(public)>/<action:(files)>/<group:\w+>' => '<controller>/<action>',
                    '<controller:(public)>/user-avatar/<filename>' => '<controller>/user-avatar',
                ]
            ],
            [
                'class' => $this->urlRuleClass,
                'pattern' => 'public/user-avatar/<filename>',
                'route' => $this->id . '/public/user-avatar'
            ],
            [
                'class' => $this->urlRuleClass,
                'pattern' => $this->id . '/login/<group>',
                'route' => $this->id . '/default/login'
            ],
            [
                'class' => 'yii\web\UrlRule',
                'pattern' => 'public/files/<path:(.*)>',
                'route' => $this->id . '/public/files'
            ],
            [
                'class' => $this->urlRuleClass,
                'pattern' => '<controller:(.*)>/__rest/<restAction>',
                'route' => '<controller>/__rest'
            ],
            [
                'class' => $this->urlRuleClass,
                'pattern' => '<controller:(.*)>/__rest/<restAction>/<_id>',
                'route' => '<controller>/__rest'
            ],
            [
                'class' => $this->urlRuleClass,
                'pattern' => $this->id . '/reports/<action:(build|export-report)>/<key>',
                'route' => $this->id . '/reports/<action>'
            ],
//            ['class' => $this->urlRuleClass, 'pattern' => $this->id . '/logout', 'route' => $this->id . '/default/logout'],
//            ['class' => $this->urlRuleClass, 'pattern' => $this->id . '/index', 'route' => $this->id . '/default/index'],
//            ['class' => $this->urlRuleClass, 'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<controller>/<action>'],
        ], false);
        parent::bootstrap($app);
    }

    public function setup_module()
    {
        RbacHelper::getAndCreate(RbacHelper::roleRoleName('superuser'), RbacHelper::TYPE_ROLE, ['description' => 'Have access to all menu in system.']);
        RbacHelper::getAndCreate(RbacHelper::userGroupRoleName('system'), RbacHelper::TYPE_ROLE, ['description' => 'User group "system".']);
        RbacHelper::addRoleCrud('/system/settings/', [RbacHelper::roleRoleName('superuser')]);
        RbacHelper::addRoleCrud('/system/reports/', [RbacHelper::roleRoleName('superuser')]);
        RbacHelper::addRoleCrud('/system/reports/', [RbacHelper::roleRoleName('superuser')], ['duplicate','template']);
        RbacHelper::addRoleCrud('/system/users-system/', [RbacHelper::roleRoleName('superuser')]);
        RbacHelper::addRoleCrud('/system/users/', [RbacHelper::roleRoleName('superuser')]);
        RbacHelper::addRoleCrud('/system/users/', [RbacHelper::roleRoleName('superuser')],['password']);
        RbacHelper::addRoleCrud('/system/activity-log/', [RbacHelper::roleRoleName('superuser')], ['index', 'view']);
        RbacHelper::addRoleCrud('/system/roles/', [RbacHelper::roleRoleName('superuser')], ['roles', 'permissions', 'assign', 'add-role', 'detail-role','users']);
        parent::setup_module();
    }
}