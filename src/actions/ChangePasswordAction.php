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
use yihai\core\log\ActivityLog;
use yihai\core\models\form\ChangePasswordForm;
use yihai\core\theming\Alert;

class ChangePasswordAction extends Action
{

    public $layout = 'backend';
    public $viewFile = '@yihai/views/_pages/change-password';

    public function run()
    {
        Yihai::$app->layout = $this->layout;

        $modelForm = new ChangePasswordForm();
        if($modelForm->load(Yihai::$app->request->post()) && $modelForm->validate()) {
            if ($modelForm->updatePassword()) {
                ActivityLog::newLog('change-password');
                Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Yihai::t('yihai', 'Success update password'), true);
            } else {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Yihai::t('yihai', 'Error update password'));
            }
            return $this->controller->redirect(['profile']);
        }
        return $this->controller->render($this->viewFile, [
            'modelForm' => $modelForm
        ]);
    }
}