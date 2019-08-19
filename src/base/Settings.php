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
use yihai\core\rbac\RbacHelper;
use yii\base\Component;

class Settings extends Component
{
    private $_modules = [];

    /**
     * @param string $module
     * @param array $roles
     * @throws \yii\base\Exception
     */
    public function addRoleModule($module, $roles)
    {
        RbacHelper::addRoleCrud('/system/settings', $roles, ['index', 'module-'.$module]);
    }

    public function addModuleSetting($module, $moduleSetting)
    {
        $this->_modules[$module] = $moduleSetting;
    }

    public function getModuleSettings()
    {
        return $this->_modules;
    }

    /**
     * @param $id
     * @return ModuleSetting|null
     */
    public function getModuleSetting($id)
    {
        if(isset($this->_modules[$id]))
            return $this->_modules[$id];
        return null;
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @throws \yii\base\Exception
     */
    public function setup($moduleSetting)
    {
        $this->addRoleModule($moduleSetting->getModuleId(), [RbacHelper::roleRoleName('superuser')]);
        $moduleSetting->setup_default();
    }

    /**
     * @param $id
     * @return ModuleSetting|null
     */
    public function loadModuleSettings($id)
    {
        if(!$setting = $this->getModuleSetting($id))
            return null;
        return $setting->loadAll();
    }

}