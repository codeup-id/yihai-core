<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\models;

use Yihai;
use Yii;

/**
 * This is the model class for table "{{%sys_menu}}".
 *
 * @property int $id
 * @property string $route
 * @property int $is_menu
 * @property int $is_group
 * @property int $parent
 * @property int $backend
 * @property int $public
 * @property int $pos
 */
class SysMenu extends \yihai\core\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sys_menu}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route', 'backend'], 'required'],
            [['is_menu', 'is_group', 'parent', 'backend', 'public', 'pos'], 'integer'],
            [['route'], 'string', 'max' => 100],
            [['route'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yihai', 'ID'),
            'route' => Yii::t('yihai', 'Route'),
            'is_menu' => Yii::t('yihai', 'Is Menu'),
            'is_group' => Yii::t('yihai', 'Is Group'),
            'parent' => Yii::t('yihai', 'Parent'),
            'backend' => Yii::t('yihai', 'Backend'),
            'public' => Yii::t('yihai', 'Public'),
            'pos' => Yii::t('yihai', 'Pos'),
        ];
    }

    public static function backendMenu($route, $pos, $access = [])
    {
        $route = rtrim($route, '/');
        $group = new static();
        $group->route = $route . '/';
        $group->is_menu = 0;
        $group->is_group = 1;
        $group->backend = 1;
        $group->pos = $pos;
        $group->public = 0;
        if ($group->save()) {
            static::addRbac($route, $access, 'role');
            $backendAction = ['index', 'create', 'update', 'delete', 'rest'];
            foreach ($backendAction as $action) {
                $pos = $pos + 1;
                $menu = new static();
                $menu->route = $route . '/' . $action;
                $menu->backend = 1;
                $menu->public = 0;
                $menu->parent = $group->id;
                if ($action === 'index')
                    $menu->is_menu = 1;
                else
                    $menu->is_menu = 0;
                $menu->pos = $pos;
                if ($menu->save()) {
                    static::addRbac($menu->route, $access, 'permission');
                }
            }
        }
    }

    private static function addRbac($route, $access, $type = 'role')
    {
        $auth = Yihai::$app->getAuthManager();
        $rbac_name = 'menu-'.$route;
        if (isset($access['*'])) {
            $roles = is_array($access['*']) ? $access['*'] : [$access['*']];
            foreach ($roles as $role) {
                if ($type === 'role') {
                    if (!$auth->getRole($rbac_name)) {
                        $auth->add($auth->createRole($rbac_name));
                    }
                    if($rbac_role = $auth->getRole($rbac_name)){
                        foreach($roles as $role){
                            if($role = $auth->getRole($role)){
                                $auth->addChild($role, $rbac_role);
                            }
                        }
                    }
                }elseif($type === 'permission'){
                    if (!$auth->getPermission($rbac_name)) {
                        $auth->add($auth->createPermission($rbac_name));
                    }
                    if($rbac_role = $auth->getPermission($rbac_name)){
                        foreach($roles as $role){
                            if($role = $auth->getRole($role)){
                                $auth->addChild($role, $rbac_role);
                            }
                        }
                    }
                }
            }
        }
    }
}