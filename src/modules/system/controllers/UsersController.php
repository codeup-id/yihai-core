<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system\controllers;


use Yihai;
use yihai\core\actions\CrudFormAction;
use yihai\core\models\form\ChangePasswordUserForm;
use yihai\core\web\BackendController;

class UsersController extends BackendController
{
    public $baseAccessAppend = [
        'password'
    ];
    /**
     * class model
     * @return string|\yihai\core\db\ActiveRecord
     */
    public function _modelClass()
    {
        return 'yihai\core\models\UserModel';
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
        $actions['password'] = [
            'class' => CrudFormAction::class,
            'formView' => '@yihai/views/_pages/change-password-user.php',
            'modelClass' => ChangePasswordUserForm::class,
            'formTitle' => Yihai::t('yihai', 'Change Password'),
            'formType' => CrudFormAction::FORM_UPDATE,
            'messageSuccess' => Yihai::t('yihai', 'Success update password')
        ];
        return $actions;
    }
}