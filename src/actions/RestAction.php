<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\actions;


use Yihai;
use yihai\core\base\Action;
use yihai\core\rbac\RbacHelper;
use yihai\core\rest\BackendActiveController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class RestAction extends CrudAction
{

    protected $type = self::TYPE_REST;

    public function init()
    {

        $this->controller->enableCsrfValidation = false;
        if (!isset(Yihai::$app->request->parsers['application/json']))
            Yihai::$app->request->parsers['application/json'] = 'yii\web\JsonParser';
        parent::init();
    }

    public function run($restAction = '___', $_id = '')
    {
//        $restConfig = ArrayHelper::merge([
//            'modelClass' => $this->modelClass,
//            ''serializer => [
//                'class' => 'yii\rest\Serializer',
//                'collectionEnvelope' => 'items',
//            ],
//            'actions' => [
//                'index' => [
//                    'dataFilter' => [
//                        'class' => 'yii\data\ActiveDataFilter',
//                        'searchModel' => $this->searchModel,
//                    ]
//                ]
//            ]
//        ], $this->restConfig);

        $restConfig = [
            'modelClass' => $this->modelClass,
            'model' => $this->model,
            'modelOptions' => $this->modelOptions,
            'backendControllerId' => $this->controller->getUniqueId()
        ];
        $restId = $this->controller->id . '/__rest';
        $activeController = new BackendActiveController($restId, $this->controller->module, $restConfig);
        return $activeController->runAction($restAction, ['id' => $_id]);
    }
}