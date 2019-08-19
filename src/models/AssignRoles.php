<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\models;


use yihai\core\base\Model;

class AssignRoles extends Model
{
    public $type;
    public $role;
    public $permission;
    public $user_id;

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['role','permission'], 'string'],
            [['type', 'user_id'], 'integer']
        ];
    }

}