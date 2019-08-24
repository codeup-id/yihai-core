<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\console\controllers;


use Yihai;
use yihai\core\console\Controller;

class ModulesController extends Controller
{
    /**
     * @var string Module name. Default is "*", all modules will be initialize
     */
    public $moduleName = "*";
    public $defaultAction = 'setup';
    /**
     * @var \yihai\core\base\Module[]
     */
    protected $yihaiModules=[];
    public function init()
    {
        parent::init();
        foreach(Yihai::$app->getModules() as $name => $config){
            if(!$this->module->hasModule($name)) continue;
            $m = $this->module->getModule($name);
            if($m instanceof \yihai\core\base\Module){
                $this->yihaiModules[$name] = $m;
            }
        }
    }
    public function options($actionID)
    {
        return array_merge(parent::options($actionID),['moduleName']);
    }

    public function actionSetup(){
        if($this->moduleName === '*'){
            foreach($this->yihaiModules as $n => $obj){
                $obj->setup_module();
                $this->stdout('Module: "'.$n.'" success setup.'."\n");
            }
        }elseif($obj = $this->getModuleObj($this->moduleName)){
            $obj->setup_module();
        }else{
            $this->stdout('Module: "'.$this->moduleName .'" Not found.'."\n");
        }
    }
    public function actionMigrateUp(){
        if($this->moduleName === '*'){
            foreach($this->yihaiModules as $n => $obj){
                $this->stdout("Migrate Up Modules \"{$n}\"\n");
                $migration = $this->getMigrationController($n);
                $migration->runAction('up');
            }
        }elseif($this->getModuleObj($this->moduleName)){
            $this->stdout("Migrate Up Modules \"{$this->moduleName}\"\n");
            $migration = $this->getMigrationController($this->moduleName);
            $migration->runAction('up');
        }else{
            $this->stdout('Module: "'.$this->moduleName .'" Not found.'."\n");
        }
    }
    public function actionMigrateDown(){
        if($this->moduleName === '*'){
            foreach($this->yihaiModules as $n => $obj){
                $this->stdout("Migrate Down Modules \"{$n}\"\n");
                $migration = $this->getMigrationController($n);
                $migration->runAction('down',[null]);
            }
        }elseif($this->getModuleObj($this->moduleName)){
            $this->stdout("Migrate Down Modules \"{$this->moduleName}\"\n");
            $migration = $this->getMigrationController($this->moduleName);
            $migration->runAction('down');
        }else{
            $this->stdout('Module: "'.$this->moduleName .'" Not found.'."\n");
        }
    }

    /**
     * @param $name
     * @return bool|\yihai\core\base\Module
     */
    protected function getModuleObj($name){
        if(isset($this->yihaiModules[$name]))
            return $this->yihaiModules[$name];
        return false;
    }

    /**
     * @param $name
     * @return MigrateController
     */
    protected function getMigrationController($name){
        if(!$module = $this->getModuleObj($name))
            $this->stderr("Not found module.");
        $class = new MigrateController('migrate-'.$name, $this);
        $class->migrationPath = [$module->getBasePath().DIRECTORY_SEPARATOR.'migrations'];
        $class->migrationNamespaces = [$module::className()];
        return $class;
    }
}