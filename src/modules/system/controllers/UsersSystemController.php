<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system\controllers;


use Yihai;
use yihai\core\web\BackendController;

class UsersSystemController extends BackendController
{

    public function _modelClass()
    {
        return '\yihai\core\models\SysUsersSystem';
    }

    /**
     * update model options
     * @param \yihai\core\base\ModelOptions $options
     * @return void
     */
    public function _modelOptions(&$options)
    {

    }

}