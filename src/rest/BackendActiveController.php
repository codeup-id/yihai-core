<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\rest;


use Yihai;
use yihai\core\base\ModelOptions;
use yihai\core\db\ActiveRecord;
use yihai\core\rbac\RbacHelper;
use yii\base\InvalidConfigException;
use yihai\core\base\Model;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;

class BackendActiveController extends Controller
{
    public $serializer = 'yihai\core\rest\Serializer';
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass;

    /**
     * @var ActiveRecord
     */
    public $model;
    /**
     * @var ModelOptions
     */
    public $modelOptions;
    /**
     * @var string the scenario used for updating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $updateScenario = Model::SCENARIO_UPDATE;
    /**
     * @var string the scenario used for creating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $createScenario = Model::SCENARIO_CREATE;

    /**
     * Id Backend Controller
     * @var string
     */
    public $backendControllerId;

    public $customAccess = null;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass === null) {
            throw new InvalidConfigException('The "modelClass" property must be set.');
        }
        if ($this->backendControllerId === null) {
            throw new InvalidConfigException('The "backendControllerId" property must be set.');
        }
        if($this->model === null)
            $this->model = new $this->modelClass();
        if(!$this->modelOptions)
            $this->modelOptions = $this->model->options();

        if($this->modelOptions->restSerializer)
            $this->serializer=$this->modelOptions->restSerializer;

    }


    public function behaviors()
    {
        return ArrayHelper::merge([
            'access' => [
                'class' => 'yihai\core\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [RbacHelper::menuRoleName($this->backendControllerId)],
                    ],
                    [
                        'allow' => true,
                        'actions' => [$this->action->id],
                        'roles' => [RbacHelper::menuRoleName($this->backendControllerId . '/' . $this->action->id)],
                    ],
                ],
            ]
        ], parent::behaviors());
    }


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yihai\core\rest\CrudIndexAction',
                'modelClass' => $this->modelClass,
                'model' => $this->model,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => 'yihai\core\rest\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => 'yii\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'delete' => [
                'class' => 'yii\rest\DeleteAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'POST', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {

    }
}