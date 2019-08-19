<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web;

use Yihai;
use yihai\core\rbac\RbacHelper;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class Menu extends BaseObject
{
    const TYPE_NOTSET = 0;
    const TYPE_HEADER = 1;
    const TYPE_GROUP = 2;
    const TYPE_MENU = 3;
    /**
     * @var string
     */
    public $id;
    /**
     * @var array|false
     */
    public $permissions = [];
    /**
     * route url
     * @var array|string
     */
    public $route;
    /**
     * @var bool|string
     */
    public $activeRoute = false;
    /**
     * @var string
     */
    public $label;
    /**
     * @var string
     */
    public $icon;
    /**
     * @var int
     */
    public $type = Menu::TYPE_NOTSET;
    /**
     * @var Menu[]
     */
    public $child = [];
    /**
     * @var int
     */
    public $position;

    /**
     * @var bool
     */
    public $isActive = false;
    /**
     * @var array
     */
    public $options = [];
    /**
     * template yang akan dipakai saat render
     * @var string
     */
    public $template = '';
    /**
     * @var bool
     */
    public $encode = true;

    public function init()
    {
        if (!$this->id && $this->route) {
            if (is_array($this->route) && isset($this->route[0]))
                $this->id = $this->route[0];
            elseif (is_string($this->route))
                $this->id = $this->route;
        }
        if (!$this->label && $this->id)
            $this->label = Inflector::camel2words($this->id);
        if (!$this->icon) {
            if ($this->type === Menu::TYPE_GROUP) $this->icon = 'menu-group';
            elseif ($this->type === Menu::TYPE_MENU) $this->icon = 'menu-item';
            elseif ($this->type === Menu::TYPE_HEADER) $this->icon = 'menu-header';
        }
        if (is_array($this->permissions) && empty($this->permissions) && !empty($this->route)) {
            if(is_array($this->route) && isset($this->route[0]))
                $this->permissions[] = RbacHelper::menuRoleName($this->route[0]);
            elseif(is_string($this->route))
                $this->permissions[] = RbacHelper::menuRoleName($this->route);
        }
        if($this->activeRoute === true){
            if(is_array($this->route) && isset($this->route[0]))
                $this->activeRoute = $this->route[0];
            elseif(is_string($this->route))
                $this->activeRoute = $this->route;
        }
        parent::init();
    }
    //------------------------------------------------------------------------------------------------

    /**
     * @var Menu
     */
    private static $list = null;

    /**
     * @param string $parent
     * @param Menu|array $menu
     */
    public static function add($parent, $menu)
    {
        if (self::$list === null) {
            self::$list = new Menu(['id' => 'yihai']);
        }
        if (is_array($menu))
            $menu = new Menu($menu);

        $parent = ($parent ? $parent . '.' . $menu->id : $menu->id);
        if (!static::getMenu($parent)) {
            static::setMenu(self::$list, $parent, $menu);
        }
    }

    /**
     * @param $key
     * @param Menu|null $menu
     * @param null $default
     * @return null|Menu
     */
    public static function getMenu($key, $menu = null, $default = null)
    {
        if (!$menu)
            $menu = self::getList();
        if (($pos = strrpos($key, '.')) !== false) {
            $menu = static::getMenu(substr($key, 0, $pos), $menu, $default);
            $key = substr($key, $pos + 1);
        }
        return (isset($menu->child[$key]) ? $menu->child[$key] : $default);
    }


    public static function setMenu(&$menu, $path, $value)
    {
        if ($path === null) {
            $menu = $value;
            return;
        }

        $keys = is_array($path) ? $path : explode('.', $path);

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($menu->child[$key])) {
                $menu->child[$key] = new Menu(['id' => $key, 'type' => self::TYPE_GROUP]);
            }
            $menu = &$menu->child[$key];

        }

        $lastKey = array_shift($keys);
        $menu->child[$lastKey] = $value;

    }

    private static function findActive($menus)
    {
        if (Yihai::$app->controller && Yihai::$app->controller->action && Yihai::$app->controller->action->getUniqueId()) {
            $action = Yihai::$app->controller->action->getUniqueId();
            foreach ($menus->child as $key => $menu) {
                echo $action . "\n";
                if ($action == $menu->route) {
                    echo $menu->route;
                    exit;
                }
                if (!empty($menu->child)) {
                    self::findActive($menu);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public static function getList()
    {
//        self::findActive(self::$list);
        return self::$list;
    }

}