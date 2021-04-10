<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web;


use Yihai;
use yihai\core\actions\CrudDeleteAction;
use yihai\core\actions\CrudExportAction;
use yihai\core\actions\CrudFormAction;
use yihai\core\actions\CrudImportAction;
use yihai\core\actions\CrudIndexAction;
use yihai\core\actions\CrudViewAction;
use yihai\core\actions\RestAction;
use yihai\core\base\CrudInterface;
use yihai\core\base\ModelOptions;
use yihai\core\rbac\RbacHelper;

abstract class BackendController extends Controller implements CrudInterface
{

    public $baseAccessAppend = [];
    public $listIndexRule = [];
    /**
     * @var ModelOptions
     */
    protected $modelOptions;
    /**
     * @var null|\yihai\core\db\ActiveRecord
     */
    protected $model = null;
    protected $modelClass;

    /**
     * BackendController constructor.
     * @param $id
     * @param $module
     * @param array $config
     */
    public function __construct($id, $module, $config = [])
    {
        Yihai::$app->layout = $this->_layout();
        parent::__construct($id, $module, $config);

    }

    public function init()
    {
        parent::init();
        $this->modelClass = $this->_modelClass();
        $this->model = new $this->modelClass();
        $modelOptions = $this->model->options();
            $this->modelOptions = $modelOptions;
        $this->modelOptions->setController($this);
        $this->_modelOptions($this->modelOptions);

        $this->listIndexRule[] = $this->modelOptions->actionDataList;
    }


    /**
     * @inheritDoc
     */
    public function _layout()
    {
        return 'backend';
    }

    /**
     * @inheritDoc
     */
    public function _isAjaxCrud()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'yihai\core\filters\AccessControl',
                'only' => array_merge($this->baseAccessAppend, [
                    $this->modelOptions->actionIndex,
                    $this->modelOptions->actionCreate,
                    $this->modelOptions->actionUpdate,
                    $this->modelOptions->actionDelete,
                    $this->modelOptions->actionView,
                    $this->modelOptions->actionImport,
                    $this->modelOptions->actionExport
                ]),
                'rules' => [
                    [
                        'controllers' => [$this->getUniqueId()],
                        'allow' => true,
                        'roles' => [RbacHelper::menuRoleName($this->getUniqueId())],
                        'roleParams'=>[
                            'action'=>$this->action->id
                        ]
                    ],
                    [
                        'controllers' => [$this->getUniqueId()],
                        'allow' => true,
                        'actions' => [$this->action->id],
                        'roles' => [RbacHelper::menuRoleName($this->action->getUniqueId())],
                    ],
                ],
            ],
            'access_data_list' => [
                'class' => 'yihai\core\filters\AccessControl',
                'only' => $this->listIndexRule,
                'rules' => [
                    [
                        'controllers' => [$this->getUniqueId()],
                        'allow' => true,
                        'roles' => [RbacHelper::menuRoleName($this->getUniqueId().'/index')],
                    ]
                ]
            ]
        ];
    }


    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if($this->modelOptions->actionDataList && ($action->id === $this->modelOptions->actionDataList)){
            $this->modelOptions->gridViewActionColumn = false;
            $this->modelOptions->gridViewSerialColumn = false;
            $this->modelOptions->gridViewCheckboxColumn = false;
        }
        return parent::beforeAction($action);
    }

    public function actions()
    {
        $actions = [];
        if ($this->modelOptions->actionIndex) {
            $actions[$this->modelOptions->actionIndex] = [
                'class' => CrudIndexAction::class,
                'modelClass' => $this->_modelClass(),
                'modelOptions' => $this->modelOptions
            ];
        }
        if ($this->modelOptions->actionDataList) {
            $actions[$this->modelOptions->actionDataList] = [
                'class' => CrudIndexAction::class,
                'modelClass' => $this->_modelClass(),
                'modelOptions' => $this->modelOptions
            ];
        }
        if ($this->modelOptions->actionCreate) {
            $actions[$this->modelOptions->actionCreate] = [
                'class' => CrudFormAction::class,
                'modelClass' => $this->_modelClass(),
                'formType' => CrudFormAction::FORM_CREATE,
                'model' => $this->model,
                'modelOptions' => $this->modelOptions
            ];
        }
        if ($this->modelOptions->actionUpdate) {
            $actions[$this->modelOptions->actionUpdate] = [
                'class' => CrudFormAction::class,
                'modelClass' => $this->_modelClass(),
                'formType' => CrudFormAction::FORM_UPDATE,
                'model' => $this->model,
                'modelOptions' => $this->modelOptions
            ];
        }
        if ($this->modelOptions->actionDelete) {
            $actions[$this->modelOptions->actionDelete] = [
                'class' => CrudDeleteAction::class,
                'modelClass' => $this->_modelClass(),
                'model' => $this->model,
                'modelOptions' => $this->modelOptions
            ];
        }
        if ($this->modelOptions->actionView) {
            $actions[$this->modelOptions->actionView] = [
                'class' => CrudViewAction::class,
                'modelClass' => $this->_modelClass(),
                'model' => $this->model,
                'modelOptions' => $this->modelOptions
            ];
        }
        if ($this->modelOptions->actionImport) {
            $actions[$this->modelOptions->actionImport] = [
                'class' => CrudImportAction::class,
                'modelClass' => $this->_modelClass(),
                'model' => $this->model,
                'modelOptions' => $this->modelOptions
            ];
        }
        if ($this->modelOptions->actionExport) {
            $actions[$this->modelOptions->actionExport] = [
                'class' => CrudExportAction::class,
                'modelClass' => $this->_modelClass(),
                'model' => $this->model,
                'modelOptions' => $this->modelOptions
            ];
        }
        if ($this->modelOptions->actionRest) {
            $actions['__rest'] = [
                'class' => RestAction::class,
                'modelClass' => $this->_modelClass(),
                'modelOptions' => $this->modelOptions
            ];
        }
        return $actions;
    }

    /**
     * @return ModelOptions
     */
    public function getModelOptions()
    {
        return $this->modelOptions;
    }


}