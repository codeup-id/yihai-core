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
use yii\base\InvalidConfigException;

class LoginAction extends Action
{
    /**
     * class login form. jika kosong berarti menggunakan class \yihai\core\models\form\LoginForm
     * @var array|string
     */
    public $loginFormClass = 'yihai\core\models\form\LoginForm';

    public $layout = '@yihai/views/_layouts/blank-with-theme';
    /**
     * @var string view file
     */
    public $view = '@yihai/views/_pages/login';

    /**
     * fungsi yang akan dipanggil ketika login berhasil. default redirect ke url sebelumnya
     * @var callable
     */
    public $on_login;

    /**
     * text pada header
     * @var string
     */
    public $header_text;

    /**
     * menampilkan checkbox remember me
     * @var bool
     */
    public $show_remember_checkbox = true;

    /**
     * placeholder untuk inputan user
     * @var string
     */
    public $placeholder_user;
    /**
     * placeholder untuk inputan password
     * @var string
     */
    public $placeholder_pass;
    /**
     * jika true, maka select group akan ditampilkan
     * @var bool
     */
    public $show_group = true;

    /**
     * menampilkan label pada input
     * @var bool
     */
    public $show_label_input = false;

    public function init()
    {
        parent::init();
        $this->controller->layout = $this->layout;
        if(!$this->on_login)
            $this->on_login = function(){
                return $this->controller->goBack();
            };
        if(!$this->header_text)
            $this->header_text = Yihai::$app->name;
        if(!$this->placeholder_user)
            $this->placeholder_user = Yihai::t('yihai', 'Nama pengguna/Email');
        if(!$this->placeholder_pass)
            $this->placeholder_pass = Yihai::t('yihai', 'Kata sandi');
    }

    /**
     * @return string|\yii\web\Response
     * @throws InvalidConfigException
     */
    public function run($group='')
    {
        // jika bukan guest atau telah menjadi user
        if (!Yihai::$app->user->isGuest)
            return $this->controller->goHome();     // redirect ke home
        $modelForm = Yihai::createObject($this->loginFormClass);
//        if ($this->loginFormClass)
//            $modelForm = new $this->loginFormClass();
//        else
//            $modelForm = new LoginForm();
        if(!$modelForm instanceof \yihai\core\base\LoginFormInterface){
            throw new InvalidConfigException(Yihai::t('yihai', '"loginFormClass" harus instance dari \yihai\core\base\LoginFormInterface'));
        }
        if ($modelForm->load(Yihai::$app->request->post()) && $modelForm->login()) {
            return call_user_func($this->on_login);
        }
        $modelForm->password = '';
        // render login page
        return $this->controller->render($this->view, [
            'model' => $modelForm,
            'group' => $group,
            'header_text' => $this->header_text,
            'show_remember_checkbox' => $this->show_remember_checkbox,
            'placeholder_user' => $this->placeholder_user,
            'placeholder_pass' => $this->placeholder_pass,
            'show_group' => $this->show_group,
            'show_label_input' => $this->show_label_input,
        ]);
    }
}