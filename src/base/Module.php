<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


use Yihai;
use yihai\core\models\SysSettings;
use yihai\core\web\Menu;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;

abstract class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @var callable
     */
    public $onBootstrap;
    /**
     * dijalankan setelah setup module. dapat digunakan untuk menambah kustom reportClass
     * ```php
     * function($module){
     *  Yihai::$app->reports->saveReport($module->id,\namespace\NamaClassReport::class, [RbacHelper::roleRoleName('superuser')]);
     * }
     * ```
     * @var callable
     */
    public $afterSetup;
    /** @var string */
    public $settingsClass;
    /** @var ModuleSetting */
    private $_settings;

    public $dashboardWidgetClass;
    public function init()
    {
        parent::init();
        if(Yihai::$app instanceof \yihai\core\web\Application)
            $this->init_web();
        if(Yihai::$app instanceof \yihai\core\console\Application)
            $this->init_console();
    }

    public function init_web()
    {
        
    }

    public function init_console()
    {

    }

    public function setup_module(){
        $this->init_component();
        if($this->_settings){
            Yihai::$app->settings->setup($this->_settings);
        }
        if($this->afterSetup){
            call_user_func($this->afterSetup, $this);
        }
    }
    public function addMenu(){

    }

    /**
     * merge app config saat memulai module
     * @param Application $app
     */
    public function init_app_config($app){

    }

    protected function init_component()
    {
        if($this->settingsClass){
            if(is_string($this->settingsClass))
                $this->_settings = Yihai::createObject(['class'=>$this->settingsClass]);
            elseif(is_array($this->settingsClass))
                $this->_settings = Yihai::createObject($this->settingsClass);
        }
        if($this->_settings){
            $this->_settings->setModuleId($this->id);
            Yihai::$app->settings->addModuleSetting($this->id, $this->_settings);
            Menu::add('backend.system.settings.modules', [
                'id' => $this->id,
                'icon' => 'setting',
                'route' => ["/system/settings/module-".$this->id],
                'activeRoute' => true,
            ]);
        }

    }

    /**
     * @return ModuleSetting|null
     */
    public static function loadSettings()
    {
        $module = static::getInstance();
        return Yihai::$app->settings->loadModuleSettings($module->id);
    }
    /**
     * @param Application $app
     */
    public function bootstrap($app){
        $this->init_component();
        if($this->onBootstrap && is_callable($this->onBootstrap)){
            call_user_func($this->onBootstrap);
        }
    }

}