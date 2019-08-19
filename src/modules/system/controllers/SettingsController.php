<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system\controllers;


use Yihai;
use yihai\core\models\SysSettings;
use yihai\core\rbac\RbacHelper;
use yihai\core\web\BackendController;

class SettingsController extends BackendController
{

    /**
     * class model
     * @return string|\yihai\core\db\ActiveRecord
     */
    public function _modelClass()
    {
        return SysSettings::class;
    }

    /**
     * update model options
     * @param \yihai\core\base\ModelOptions $options
     * @return void
     */
    public function _modelOptions(&$options)
    {
    }

    public function actions()
    {
        $actions = parent::actions();
        foreach(Yihai::$app->settings->getModuleSettings() as $module => $setting){
            if(Yihai::$app->user->can(RbacHelper::menuRoleName('system/settings/module-'.$module))) {
                $actions['module-' . $module] = [
                    'class' => 'yihai\core\actions\ModuleSettingsAction',
                    'module' => $module
                ];
            }
        }
        return $actions;
    }

}