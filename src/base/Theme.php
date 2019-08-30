<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


use Yihai;
use yihai\core\base\ThemeInterface;
use Yii;
use yii\base\BaseObject;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class Theme extends Component
{
    public $list = [];
    public $active = 'defyihai';
    public $pathMap = [];
    /** @var ThemeInterface */
    public $activeTheme;

    private $path = [];

    private $_theme = [];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        foreach ($this->list as $class) {
            if(is_string($class)) {
                if (!class_exists($class)) continue;
                /** @var ThemeInterface $classObj */
                $classObj = new $class();
            }elseif(is_array($class)){
                $classObj = Yihai::createObject($class);
            }else{
                throw new InvalidConfigException('Theme list item must array or string');
            }
            if (!$classObj instanceof ThemeInterface)
                throw new InvalidConfigException("Theme list class is not instance of ThemeInterface");
            $this->initialize_theme($classObj);
        }
    }

    public function init_active()
    {
        $this->initialize_active();
    }
    /**
     * @param ThemeInterface $classObj
     */
    private function initialize_theme($classObj)
    {
        Yii::setAlias('@yihai-theme-' . $classObj->getName(), $classObj->getPath());
        $this->path[$classObj->getName()] = [
            'views' => $classObj->getPath() . '/views'
        ];
        $this->_theme[$classObj->getName()] = $classObj;
        foreach ($classObj->getContainer() as $class => $definition) {
            Yii::$container->set($class, $definition);
        }
    }

    public function initialize_active()
    {
        $theme = $this->getActiveClass();
        $this->activeTheme = $theme;
        Yii::setAlias('@yihai-active-theme', $theme->getPath());
        Yihai::$app->view->theme->setBasePath($theme->getPath());
        Yihai::$app->view->theme->setBaseUrl('@web/themes/' . $this->active);
        $pathMap = [];
        $pathMap['@yii/views'] = $pathMap['@yihai/views'] = [
            '@yihai-active-theme/views',
            '@yihai-theme-defyihai/views',
        ];
        $pathMap = array_merge($this->pathMap, $pathMap);
        $pathMap = array_merge($pathMap, $theme->getPathMap());
        Yihai::$app->view->theme->pathMap = $pathMap;

    }

    public function activeAlias()
    {
        return '@yihai-theme-' . $this->active;
    }

    /**
     * @param string $active theme name
     */
    public function set($active = '')
    {
        if ($active == '')
            return;
        $this->active = $active;
        $this->initialize_active();
    }

    /**
     * @return ThemeInterface
     */
    public function getActiveClass()
    {

        return $this->_theme[$this->active];
    }

    public function pathView($view)
    {
        $path = $this->path[$this->active];
        return $path['views'] . '/' . $view;
    }

}