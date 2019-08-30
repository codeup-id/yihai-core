<?php
namespace yihai\core\actions;
use Yihai;
use yihai\core\theming\Alert;
use yii\web\MethodNotAllowedHttpException;

/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */


class CrudDeleteAction extends \yihai\core\actions\CrudAction{

    /**
     * @return bool
     * @throws MethodNotAllowedHttpException
     */
    protected function beforeRun()
    {
        $verb = Yihai::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', ['POST']);
        if (!in_array($verb, $allowed)) {
            // https://tools.ietf.org/html/rfc2616#section-14.7
            Yihai::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $allowed));
            throw new MethodNotAllowedHttpException('Method Not Allowed. This URL can only handle the following request methods: ' . implode(', ', $allowed) . '.');
        }
        return parent::beforeRun();
    }
    public function run(){
        $multiples = Yihai::$app->request->getBodyParam('multiple');
        if($multiples) {
            $deleted = false;
            $model = $this->model;
            $primaryKeys = $model::primaryKey();
            foreach(json_decode($multiples, true) as $multiple){
                if(is_string($multiple) || is_int($multiple)){
                    $multiple_array =[];
                    foreach($primaryKeys as $pk){
                        $multiple_array[$pk] = $multiple;
                    }
                    $multiple = $multiple_array;
                }
                if ($this->findModelDelete($multiple)->delete()) {
                    $deleted = true;
                }
            }
            if($deleted){
                Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Yihai::t('yihai', 'Sukses menghapus items'), true);
            }else{
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Yihai::t('yihai', 'Gagal menghapus items'), true);
            }
        }else{
            $id = Yihai::$app->request->getQueryParams();
            unset($id[Yihai::$app->urlManager->routeParam]);
            if ($this->findModelDelete($id)->delete()) {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Yihai::t('yihai', 'Sukses menghapus item'), true);
            }else{
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Yihai::t('yihai', 'Gagal menghapus item'), true);
            }
        }
        return $this->controller->redirect($this->modelOptions->getActionUrlTo('index'));
    }
}