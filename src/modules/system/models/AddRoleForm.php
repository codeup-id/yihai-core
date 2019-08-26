<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system\models;


use Yihai;
use yihai\core\base\Model;
use yihai\core\rbac\RbacHelper;
use yii\helpers\StringHelper;

class AddRoleForm extends Model
{
    public $oldName;
    public $isUpdating = false;
    public $name;
    public $description;
    public function rules()
    {
        return [
            [['name', 'description'], 'required']
        ];
    }

    public function save()
    {
        $am = Yihai::$app->getAuthManager();
        $custom_role_name = RbacHelper::roleRoleCustomName($this->name);
        if($am->getRole($custom_role_name)){
            $this->addError('name', Yihai::t('yihai', 'Custom Role "{name}" already exist.',['name'=>$this->name]));
            return false;
        }
        try {
            $role = $am->createRole($custom_role_name);
            if ($role) {
                $role->description = $this->description;
                if($am->add($role)) return true;
            }
        }catch (\Exception $e){}
        return false;
    }

    public function update()
    {
        $am = Yihai::$app->getAuthManager();
        $custom_role_name = RbacHelper::roleRoleCustomName($this->name);
        try {
            $role = $am->getRole($this->oldName);
            if ($role) {
                $role->name = $custom_role_name;
                $role->description = $this->description;
                if($am->update($this->oldName,$role)) return true;
            }
        }catch (\Exception $e){}
        return false;
    }

    public function delete()
    {
        try {
            $am = Yihai::$app->getAuthManager();
            $custom_role_name = RbacHelper::roleRoleCustomName($this->name);
            if ($role = $am->getRole($custom_role_name)) {
                if($am->remove($role))
                    return true;
            }
        }catch(\Exception $e){}
        return false;
    }
}