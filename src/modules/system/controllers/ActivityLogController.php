<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system\controllers;


use yihai\core\web\BackendController;

class ActivityLogController extends BackendController
{

    /**
     * class model
     * @return string|\yihai\core\db\ActiveRecord
     */
    public function _modelClass()
    {
        return '\yihai\core\log\ActivityLog';
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