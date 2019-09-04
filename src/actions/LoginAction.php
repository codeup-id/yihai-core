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
use yihai\core\models\form\LoginForm;

class LoginAction extends Action
{
    /**
     * class login form. jika kosong berarti menggunakan class LoginForm
     * @var string
     */
    public $loginFormClass = '';

    public $layout = '@yihai/views/_layouts/blank-with-theme';
    /**
     * @var string view file
     */
    public $view = '@yihai/views/_pages/login';
    public function init()
    {
        parent::init();
        $this->controller->layout = $this->layout;
    }

    /**
     * @return string|\yii\web\Response
     */
    public function run($group='')
    {
        // jika bukan guest atau telah menjadi user
        if (!Yihai::$app->user->isGuest)
            return $this->controller->goHome();     // redirect ke home

        if ($this->loginFormClass)
            $modelForm = new $this->loginFormClass();
        else
            $modelForm = new LoginForm();
        if ($modelForm->load(Yihai::$app->request->post())) {
//            print_r($modelForm->group);exit;
        }
        if ($modelForm->load(Yihai::$app->request->post()) && $modelForm->login()) {
            return $this->controller->goBack();
        }
        $modelForm->password = '';
        // render login page
        return $this->controller->render($this->view, [
            'model' => $modelForm,
            'group' => $group
        ]);
    }
}