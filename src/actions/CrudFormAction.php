<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\actions;


use Yihai;
use yihai\core\base\Model;
use yihai\core\theming\Alert;
use yihai\core\theming\Html;

class CrudFormAction extends CrudAction
{
    protected $type = parent::TYPE_FORM;

    public $viewFile;

    /**
     * @var string path form view file
     */
    public $formView;
    /**
     * @var array|\yihai\core\theming\ActiveForm
     */
    public $formConfig = [];

    public $messageSuccess = 'Success';
    public $messageError = 'Error';

    public function init()
    {
        if ($this->viewFile)
            $this->modelOptions->viewFileForm = $this->viewFile;

        parent::init();

        if ($this->formView === null) {
            $this->formView = $this->controller->getViewPath() . '/_form.php';
        }
        $this->addParams('formViewFile', $this->formView);
        if ($this->formConfig) {
            $this->modelOptions->formConfig = $this->formConfig;
        }
    }

    public function run()
    {
        if ($this->model->load(Yihai::$app->request->post())) {
            if ($this->model->validate()) {
                if ($this->model->save(false)) {
                    $msg = ($this->messageSuccess ? $this->messageSuccess : Yihai::t('yihai', 'Sukses'));
                    Alert::addFlashAlert(Alert::KEY_CRUD, 'success', $msg, true);
                    return $this->controller->redirect($this->redirect);
                }
            } else {
                $msg = ($this->messageError ? $this->messageError : Yihai::t('yihai', 'Kesalahan'));
                $messageDangers = [Html::tag('b', $msg)];
                foreach ($this->model->getErrors() as $attribute => $err) {
                    $messageDangers[] = implode('<br/>', $err);
                }
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', implode('<br/>', $messageDangers));
            }
        }
        return parent::run();
    }

}