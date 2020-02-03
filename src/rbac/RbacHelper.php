<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\rbac;


use Exception;
use Yihai;
use yii\helpers\StringHelper;
use yii\rbac\Item;

class RbacHelper
{

    const TYPE_ROLE = Item::TYPE_ROLE;
    const TYPE_PERMISSION = Item::TYPE_PERMISSION;

    public static $idRoleUser = '_user-';
    public static $idRoleRole = '_role-';
    public static $idRoleMenu = '_menu-';
    public static $idRoleModule = '_module-';
    public static $idRoleCustom = '_custom_role-';

    public static function userGroupRoleName($group)
    {
        return static::$idRoleUser . $group;
    }

    public static function roleRoleName($role)
    {
        return static::$idRoleRole . $role;
    }


    public static function roleModuleName($module, $role = '')
    {
        $module = trim($module, '-');
        $role = ($role === '') ? $module : $module . '-' . $role;
        return static::$idRoleModule . $role;
    }

    public static function roleRoleCustomName($role)
    {
        return static::$idRoleCustom . $role;
    }

    public static function menuRoleName($menu)
    {
        $menu = '/' . ltrim($menu, '/');
        return static::$idRoleMenu . $menu;
    }

    /**
     * @param string $role
     * @return bool
     */
    public static function roleIsUserGroupName($role)
    {
        return StringHelper::startsWith($role, static::$idRoleUser);
    }

    /**
     * @param string $role
     * @return bool
     */
    public static function roleIsRoleName($role)
    {
        return StringHelper::startsWith($role, static::$idRoleRole);
    }

    /**
     * @param string $role
     * @return bool
     */
    public static function roleIsMenuName($role)
    {
        return StringHelper::startsWith($role, static::$idRoleMenu);
    }

    /**
     * @param string $role
     * @return bool
     */
    public static function roleIsCustomName($role)
    {
        return StringHelper::startsWith($role, static::$idRoleCustom);
    }

    /**
     * @param string $menu
     * @return bool
     */
    public static function checkUserCanMenu($menu)
    {
        if (Yihai::$app->user->isGuest) return false;
        if (Yihai::$app->user->can(self::menuRoleName($menu)))
            return true;
        return false;
    }

    /**
     * @param Item $parent
     * @param Item $child
     * @throws \yii\base\Exception
     */
    public static function addChild($parent, $child)
    {
        $auth = Yihai::$app->getAuthManager();
        if (!$auth->hasChild($parent, $child) && $auth->canAddChild($parent, $child)) {
            $auth->addChild($parent, $child);
        }
    }

    /**
     * @param string $name role/permission name
     * @param int $type Item::TYPE_ROLE|Item::TYPE_PERMISSION
     * @param array|Item $attributes
     * @return \yii\rbac\Permission|\yii\rbac\Role|null
     * @throws Exception
     */
    public static function getAndCreate($name, $type = Item::TYPE_ROLE, $attributes = [])
    {
        $created = FALSE;
        $auth = Yihai::$app->getAuthManager();
        if ($type === Item::TYPE_ROLE) {
            $role = $auth->getRole($name);
            if (!$role) {
                $role = $auth->createRole($name);
                $created = TRUE;
            }
        } else {
            $role = $auth->getPermission($name);
            if (!$role) {
                $role = $auth->createPermission($name);
                $created = TRUE;
            }
        }
        if ($created) {
            if (is_array($attributes)) {
                foreach ($attributes as $key => $value) {
                    if ($role->canSetProperty($key)) {
                        $role->{$key} = $value;
                    }
                }
            }
            $auth->add($role);
        }
        return $role;
    }

    /**
     * menambah action serta parent
     * @param string|array $route
     * @param array $roles main role
     * @param array $actions
     * @param string $description
     * @throws \yii\base\Exception
     */
    public static function addRoleCrud($route, $roles = [], $actions = [], $description = 'Main CRUD')
    {
        if (is_array($route)) {
            foreach ($route as $r) {
                static::addRoleCrud($r, $roles, $actions);
            }
            return;
        }
        if (empty($actions)) {
            $actions = ['index', 'create', 'update', 'delete', 'view', 'import'];
        } elseif ($actions === false) {
            $actions = [];
        }
        $route = '/' . trim($route, '/');
        $menu_role = static::getAndCreate(static::menuRoleName($route), Item::TYPE_PERMISSION, ['description' => $description]);
        if (!empty($roles)) {
            foreach ($roles as $role) {
                $main_role = static::getAndCreate($role, Item::TYPE_ROLE);
                static::addChild($main_role, $menu_role);
            }
        }
        foreach ($actions as $action) {
            $permission_name = static::menuRoleName($route . '/' . trim($action, '/'));
            $permission = static::getAndCreate($permission_name, Item::TYPE_PERMISSION);
            static::addChild($menu_role, $permission);
        }
    }

    public static function addRoleCrudModule($moduleId, $route, $roles)
    {
        static::addRoleCrud($route);
    }

    public static function addRoleCrudRest($route, $roles = [], $actions = [])
    {
        $route = '/' . trim($route, '/') . '/rest';

        self::addRoleCrud($route, $roles, $actions);
    }

    /**
     * Hanya menambah role ke action
     * @param string $route
     * @param array $roles
     * @param array $actions
     * @throws \yii\base\Exception
     */
    public static function addRoleToMenuAction($route, $roles, $actions)
    {
        $route = '/' . trim($route, '/');

        foreach ($actions as $action) {
            $permission_name = static::menuRoleName($route . '/' . trim($action, '/'));
            $permission = static::getAndCreate($permission_name, Item::TYPE_PERMISSION);
            foreach ($roles as $role) {
                $parent = static::getAndCreate($role);
                static::addChild($parent, $permission);
            }
        }
    }

    /**
     * @param string $roleName
     * @param int $user_id
     * @throws Exception
     */
    public static function forceAssignRole($roleName, $user_id)
    {
        $auth = Yihai::$app->getAuthManager();
        $role = static::getAndCreate($roleName, self::TYPE_ROLE);
        try {
            $auth->assign($role, $user_id);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public static function forceRemove($role, $type = 0)
    {
        try {
            $auth = Yihai::$app->getAuthManager();
            if ($type === self::TYPE_ROLE) {
                $_role = $auth->getRole($role);
            } elseif ($type === self::TYPE_PERMISSION) {
                $_role = $auth->getPermission($role);
            } else {
                if (!$_role = $auth->getPermission($role)) {
                    $_role = $auth->getRole($role);
                }
            }
            $auth->remove($_role);
        } catch (Exception $e) {
        }

    }

    public static function getRoles()
    {
        $auth = Yihai::$app->getAuthManager();
        return $auth->getRoles();
    }

    public static function getRolesExcludeUserGroup()
    {
        $roles = static::getRoles();
        foreach ($roles as $name => $role) {
            if (static::roleIsUserGroupName($name))
                unset($roles[$name]);
        }
        return $roles;
    }

    /**
     * @param $userId
     * @return \yii\rbac\Role[]
     */
    public static function getUserRoles($userId)
    {
        $am = Yihai::$app->getAuthManager();
        return $am->getRolesByUser($userId);
    }


    public static function getRolesRole()
    {
        $r = [];
        foreach (static::getRoles() as $role) {
            if (RbacHelper::roleIsRoleName($role->name)) {
                $r[] = $role;
            }
        }
        return $r;
    }
}