<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


use Yihai;
use yihai\core\web\Menu;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class SidebarMenu extends BaseWidget
{
    /**
     * @var Menu
     */
    public $items = [];
    public $submenuTemplate = "\n<ul class='treeview-menu' {show}>\n{items}\n</ul>\n";
    /**
     * @var bool whether the labels for menu items should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var string the CSS class to be appended to the active menu item.
     */
    public $activeCssClass = 'active';
    /**
     * @var bool whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive()
     */
    public $activateItems = true;
    /**
     * @var bool whether to activate parent menu items when one of the corresponding child menu items is active.
     * The activated parent menu items will also have its CSS classes appended with [[activeCssClass]].
     */
    public $activateParents = true;
    /**
     * @var bool whether to hide empty menu items. An empty menu item is one whose `url` option is not
     * set and which has no visible child menu items.
     */
    public $hideEmptyItems = true;
    /**
     * @var array the HTML attributes for the menu's container tag. The following special options are recognized:
     *
     * - tag: string, defaults to "ul", the tag name of the item container tags. Set to false to disable container tag.
     *   See also [[\yii\helpers\Html::tag()]].
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'sidebar-menu', 'data-widget' => 'tree'];
    /**
     * @var string the CSS class that will be assigned to the first item in the main menu or each submenu.
     * Defaults to null, meaning no such CSS class will be assigned.
     */
    public $firstItemCssClass;
    /**
     * @var string the CSS class that will be assigned to the last item in the main menu or each submenu.
     * Defaults to null, meaning no such CSS class will be assigned.
     */
    public $lastItemCssClass;
    /**
     * @var string the route used to determine if a menu item is active or not.
     * If not set, it will use the route of the current request.
     * @see params
     * @see isItemActive()
     */
    public $route;
    /**
     * @var array the parameters used to determine if a menu item is active or not.
     * If not set, it will use `$_GET`.
     * @see route
     * @see isItemActive()
     */
    public $params;

    private $noDefaultAction;
    private $noDefaultRoute;

    /**
     * option untuk semua item
     * @var array
     */
    public $itemOptions = [];

    /**
     * header item options
     * ```php
     * [
     *  'class' => 'main class',
     *  'activeClass' => 'active css class',
     *  'template' => ''
     * ]
     * ```
     * @var array
     */
    public $headerItemOptions = [
        'class' => 'header',
        'template' => '{icon} {label}',
    ];
    /**
     * group item options
     * @var array
     */
    public $groupItemOptions = [
        'class' => 'treeview',
        'template' => '<a href="{url}">{icon} <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a><ul class=\'treeview-menu\' {show}>{items}</ul>'
    ];
    /**
     * menu item options
     * @var array
     */
    public $menuItemOptions = [
        'template' => '<a href="{url}">{icon} <span>{label}</span></a>'
    ];
    /**
     * nama class yang akan dipakai di "li" ketika sub menu aktif.
     * @var string
     */
    public $groupMenuOpenClass = '';

    /**
     * Renders the menu.
     */
    public function run()
    {
        if ($this->route === null && Yihai::$app->controller !== null) {
            $this->route = Yihai::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yihai::$app->request->getQueryParams();
        }
        $posDefaultAction = strpos($this->route, Yihai::$app->controller->defaultAction);
        if ($posDefaultAction) {
            $this->noDefaultAction = rtrim(substr($this->route, 0, $posDefaultAction), '/');
        } else {
            $this->noDefaultAction = false;
        }
        $posDefaultRoute = strpos($this->route, Yihai::$app->controller->module->defaultRoute);
        if ($posDefaultRoute) {
            $this->noDefaultRoute = rtrim(substr($this->route, 0, $posDefaultRoute), '/');
        } else {
            $this->noDefaultRoute = false;
        }
        $items = $this->normalizeItems($this->items, $hasActiveChild);
        if (!empty($items)) {
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'ul');
            echo Html::tag($tag, $this->renderItems($items), $options);
        }
    }

    /**
     * Recursively renders the menu items (without the container tag).
     * @param Menu[] $items the menu items to be rendered recursively
     * @return string the rendering result
     */
    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {

            ArrayHelper::multisort($item->child, ['position']);
            if ($item->type === Menu::TYPE_HEADER) {
                $itemOptions = array_merge($this->itemOptions, $this->headerItemOptions);
            } elseif ($item->type === Menu::TYPE_GROUP) {
                $itemOptions = array_merge($this->itemOptions, $this->groupItemOptions);
                if ($this->groupMenuOpenClass && $item->isActive) {
                    $itemOptions['_class'] = [$this->groupMenuOpenClass];
                }
            } else {
                $itemOptions = array_merge($this->itemOptions, $this->menuItemOptions);
            }

            $options = array_merge($itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $template = ArrayHelper::remove($options, 'template', '');
            $activeClass = ArrayHelper::remove($options, 'activeClass', $this->activeCssClass);
            $class = [];
            if ($item->isActive) {
                $class[] = $activeClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }
            $_class = ArrayHelper::remove($options, '_class', []);
            Html::addCssClass($options, $_class);
            $replacements = [
                '{label}' => $item->label,
                '{icon}' => Html::icon($item->icon, ['tag' => 'i']),
                '{url}' => $item->route ? Url::to($item->route) : 'javascript:void(0);',
                '{items}' => (!empty($item->child) ? $this->renderItems($item->child) : ''),
                '{activeClass}' => $item->isActive ? $activeClass : '',
            ];
            $build = strtr($template, $replacements);
            $lines[] = Html::tag($tag, $build, $options);
            if ($item->type === Menu::TYPE_HEADER) {
                $lines[] = $replacements['{items}'];
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param Menu $items
     * @param $active
     * @return array
     */
    protected function normalizeItems($items, &$active)
    {
        foreach ($items->child as $i => $item) {
            if (!empty($item->permissions)) {
                $can = false;
                foreach ($item->permissions as $permission) {
                    if (Yihai::$app->user->can($permission,['menu'=>$item,'permission'=>$permission])) {
                        $can = true;
                        continue;
                    }
                }
                if (!$can) {
                    unset($items->child[$i]);
                    continue;

                }
            }
            if (!($item->label)) {
                $item->label = '';
            }

            $encodeLabel = (is_bool($item->encode)) ? $item->encode : $this->encodeLabels;
            $items->child[$i]->label = $encodeLabel ? Html::encode($item->label) : $item->label;
            $items->child[$i]->icon = $item->icon ? $item->icon : '';
            $hasActiveChild = false;
            if (!empty($item->child)) {

                $items->child[$i]->child = $this->normalizeItems($item, $hasActiveChild);
                if (empty($items->child[$i]->child) && $this->hideEmptyItems) {
                    unset($items->child[$i]->child);
                    if (!($item->route)) {
                        unset($items->child[$i]);
                        continue;
                    }
                }
            }
            if (!$item->isActive) {
                if ($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item)) {
                    $active = $items->child[$i]->isActive = true;
                } else {
                    $items->child[$i]->isActive = false;
                }
            } elseif ($item->isActive) {
                $active = true;
            }
        }
        return array_values($items->child);
    }

    private $_active_route = [];

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param Menu $item the menu item to be checked
     * @return boolean whether the menu item is active
     */
    protected function isItemActive($item)
    {
        if ($item->route && is_array($item->route) && isset($item->route[0])) {
            $route = $item->route[0];
            if (isset($route[0]) && $route[0] !== '/' && Yihai::$app->controller) {
                $route = ltrim(Yihai::$app->controller->module->getUniqueId() . '/' . $route, '/');
            }
            $route = ltrim($route, '/');
            $unique_route = Yihai::$app->controller->getRoute();
            if ($item->activeRoute) {
                $activeRoute = ltrim($item->activeRoute, '/');
                if (
                    $activeRoute === $unique_route
                    ||
                    (strpos($activeRoute, $unique_route) !== false)
                ) {
                    $this->_active_route[] = $activeRoute;
                    return true;
                }
            }
            foreach ($this->_active_route as $_active_route) {
                if (strpos($_active_route, $unique_route) !== false) {
                    return false;
                }
            }
            if (Yihai::$app->controller->getUniqueId() == $route)
                return true;

            if ($route != $this->route && $route !== $this->noDefaultRoute && $route !== $this->noDefaultAction) {
                return false;
            }
            unset($item->route['#']);
            if (count($item->route) > 1) {
                foreach (array_splice($item->route, 1) as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

}