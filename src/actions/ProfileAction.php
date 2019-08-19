<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\actions;


use Yihai;
use yihai\core\base\Action;

class ProfileAction extends Action
{

    public $layout = 'backend';
    public $viewFile = '@yihai/views/_pages/profile';

    public function run()
    {
        Yihai::$app->layout = $this->layout;

        return $this->controller->render($this->viewFile);
    }
}