<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\rest;


use yihai\core\base\ModelOptions;

class CrudAction extends Action
{

    /** @var \yihai\core\db\ActiveRecord */
    public $model;
    /** @var ModelOptions */
    public $modelOptions;
}