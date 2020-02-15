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
use yihai\core\base\UserIdent;
use yihai\core\models\form\ChangePasswordUserForm;
use yihai\core\web\BackendController;
use yii\web\NotFoundHttpException;

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

    public function actionSwitch($id = '')
    {
        if(($id==='_back') && Yihai::$app->session->has('user_switch_from')) {
            $session = Yihai::$app->session->get('user_switch_from');
            Yihai::$app->user->switchIdentity(UserIdent::findByID($session['id']));
            Yihai::$app->session->remove('user_switch_from');
            return $this->redirect($session['last_url']);
        }

        if(!Yihai::$app->user->can('_menu-/system/users/switch')){
            throw new NotFoundHttpException(Yihai::t('yii', 'Page not found.'));
        }
        if(!$id) throw new NotFoundHttpException(Yihai::t('yii', 'Page not found.'));
        $userNew = UserIdent::findByID($id);
        if(!$userNew) throw new NotFoundHttpException(Yihai::t('yii', 'Page not found.'));
        $userFrom = Yihai::$app->user->identity->model;
        Yihai::$app->session->set('user_switch_from', [
            'id' => $userFrom->id,
            'username' => $userFrom->username,
            'group' => $userFrom->group,
            'last_url' => Yihai::$app->request->referrer ?: Yihai::$app->homeUrl
        ]);
        Yihai::$app->user->switchIdentity($userNew);
        return $this->goHome();
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['password'] = [
            'class' => CrudFormAction::class,
            'formView' => '@yihai/views/_pages/change-password-user.php',
            'modelClass' => ChangePasswordUserForm::class,
            'formTitle' => Yihai::t('yihai', 'Ganti kata sandi'),
            'formType' => CrudFormAction::FORM_UPDATE,
            'messageSuccess' => Yihai::t('yihai', 'Sukses mengganti kata sandi')
        ];
        return $actions;
    }
}