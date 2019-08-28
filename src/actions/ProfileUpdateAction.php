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
use yihai\core\theming\Alert;
use yii\web\NotFoundHttpException;

class ProfileUpdateAction extends Action
{

    public $layout = 'backend';
    public $viewFile = '@yihai/views/_pages/profile-update';

    public function run()
    {
        Yihai::$app->layout = $this->layout;
        $model = Yihai::$app->user->identity->data;
        if(!$model){
            throw new NotFoundHttpException(Yihai::t('yihai', 'Data pengguna tidak ditemukan'));
        }
        if($model->load(Yihai::$app->request->post()) && $model->validate()){
            if($model->save()){

                Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Yihai::t('yihai', 'Sukses perbarui'),true);
                return $this->controller->redirect('profile');
            }else{
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Yihai::t('yihai', 'Tidak dapat memperbarui'));
            }
        }
        $params = [
            'model' => $model,
        ];
        if($model->updateFormFile() && file_exists(Yihai::getAlias($model->updateFormFile()))){
            $params['formView'] = $model->updateFormFile();
        }
        return $this->controller->render($this->viewFile,$params);
    }

}