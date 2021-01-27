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
use yihai\core\base\CrudInterface;
use yihai\core\base\ModelOptions;
use yihai\core\base\Model;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class CrudAction
 * @package yihai\core\actions
 * @property CrudInterface|\yihai\core\web\Controller $controller
 */
class CrudAction extends Action
{
    const TYPE_INDEX = 1;
    const TYPE_FORM = 2;
    const TYPE_VIEW = 3;
    const TYPE_IMPORT = 4;
    const TYPE_EXPORT = 5;
    const TYPE_REST = 6;

    const FORM_CREATE = Model::SCENARIO_CREATE;
    const FORM_UPDATE = Model::SCENARIO_UPDATE;
    /**
     * @var ModelOptions
     */
    public $modelOptions;
    /**
     * Action Type
     * @var string
     */
    protected $type;
    /**
     * @var string [FORM_CREATE|FORM_UPDATE]
     */
    public $formType = self::FORM_CREATE;

    public $formTitle;
    public $baseViewFile = '@yihai/views/_pages/crud-base';

    /**
     * string model class name
     * @var string|\yihai\core\db\ActiveRecord
     */
    public $modelClass;
    /**
     * @var null|\yihai\core\db\ActiveRecord
     */
    public $model;
    /**
     * akan di merge pada findmodel
     * @var array
     */
    public $findParams = [];
    /**
     * view path
     * @var string
     */
    public $viewFile;

    /**
     * params yang dipakai pada view
     * @var array
     */
    private $_params = [];

    public $scenarioAttributes = [];

    public $redirect;

    protected $queryParams;

    /**
     * CrudAction constructor.
     * @param $id
     * @param $controller
     * @param array $config
     */
    public function __construct($id, $controller, $config = [])
    {
        parent::__construct($id, $controller, $config);

    }

    public function exception_limit()
    {
        try {
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            ini_set("pcre.backtrack_limit", "5000000");
        }catch (\Exception $e){}
    }
    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass === NULL)
            throw new InvalidConfigException('modelClass must define');

        if ($this->findParams) $this->modelOptions->findParams = $this->findParams;
        $queryParams = Yihai::$app->request->getQueryParams();
        if(isset($queryParams[Yihai::$app->urlManager->routeParam]))
            unset($queryParams[Yihai::$app->urlManager->routeParam]);
        // remove custom params
        foreach($queryParams as $key => $val){
            if(StringHelper::startsWith($key, '__'))
                unset($queryParams[$key]);
        }
        $this->queryParams = $queryParams;
        if (($this->type === self::TYPE_FORM && $this->formType === self::FORM_UPDATE) || $this->type === self::TYPE_VIEW) {
            $this->model = $this->findModel($queryParams);
        } else {
            if (!$this->model)
                $this->model = new $this->modelClass();
        }
        if (!$this->modelOptions) {
            if (is_array($this->model->options()))
                $this->modelOptions = Yihai::createObject($this->model->options());
            else
                $this->modelOptions = $this->model->options();
            $this->modelOptions->setController($this->controller);
        }
        if ($queryRedirect = Yihai::$app->request->getQueryParam('__redirect')) {
            $this->redirect = $queryRedirect;
        }
        if ($this->redirect)
            $this->modelOptions->redirect = $this->redirect;
        else {
            if ($this->modelOptions->redirect)
                $this->redirect = $this->modelOptions->redirect;
            else {
                $this->redirect = [$this->modelOptions->actionIndex];
                $this->modelOptions->redirect = [$this->modelOptions->actionIndex];
            }
        }
        if ($this->type === self::TYPE_FORM) {
            $this->viewFile = $this->modelOptions->viewFileForm;
            $this->init_form();
        } elseif ($this->type === self::TYPE_VIEW) {
            $this->viewFile = $this->modelOptions->viewFileView;
            $this->addParams('formTitle', Yihai::t('yihai', 'Lihat Data'));
        } elseif ($this->type === self::TYPE_IMPORT) {
            $this->viewFile = $this->modelOptions->viewFileImport;
            $this->addParams('formTitle', Yihai::t('yihai', 'Impor Data'));
        } elseif ($this->type === self::TYPE_EXPORT) {
            $this->viewFile = $this->modelOptions->viewFileExport;
            $this->addParams('formTitle', Yihai::t('yihai', 'Ekspor Data'));
            $this->addParams('formButton', Yihai::t('yihai', 'Ekspor'));
        } else {
            $this->viewFile = $this->modelOptions->viewFileIndex;
        }

        if (strrchr($this->viewFile, ".") !== '.php') {
            $this->viewFile .= '.php';
        }

    }
    protected function init_form()
    {
        if ($this->formType === self::FORM_CREATE) {
            $this->model->loadDefaultValues();
            $this->addParams('formTitle', Yihai::t('yihai', 'Formulir (Tambah)'));
            $this->addParams('formButton', Yihai::t('yihai', 'Simpan'));

        } elseif ($this->formType === self::FORM_UPDATE) {
            $this->addParams('formTitle', Yihai::t('yihai', 'Formulir (Perbarui)'));
            $this->addParams('formButton', Yihai::t('yihai', 'Perbarui'));
        }
        // menambah scenario
        $this->model->addScenario($this->formType, $this->scenarioAttributes);
        // set scenario
        $this->model->scenario = $this->formType;

    }

    public function run()
    {
        $this->addParams([
            'modelOptions' => $this->modelOptions,
            'viewFile' => $this->viewFile,
            'modelClass' => $this->modelClass,
            'model' => $this->model,
            'formType' => $this->formType
        ]);
        $this->addParams('formTitle',($this->formTitle?$this->formTitle:($this->getParams('formTitle')?$this->getParams('formTitle'):'')));
        $this->modelOptions->viewParams = $this->getParams();
        if (Yihai::$app->request->isAjax || Yihai::$app->request->isPjax) {
            Yihai::$app->response->format = Response::FORMAT_JSON;
            $content = [
                'type' => $this->type,
                'formTitle' => $this->getParams('formTitle'),
                'html' => $this->controller->renderAjax($this->baseViewFile, $this->_params)
            ];
            return $content;
        }
        return $this->controller->render($this->baseViewFile, $this->_params);
    }

    /**
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function getParams($key = null, $default = null)
    {
        if ($key) {
            if (isset($this->_params[$key]))
                return $this->_params[$key];
            return $default;
        }
        return $this->_params;
    }

    /**
     * @param array|string $key
     * @param string $value
     */
    public function addParams($key, $value = '')
    {
        if (is_array($key) && $value === '') {
            $this->_params = ArrayHelper::merge($this->_params, $key);
        } else {
            $this->_params[$key] = $value;
        }
    }


    /**
     * @param $params
     * @return string|\yihai\core\db\ActiveRecord|null
     * @throws NotFoundHttpException
     */
    protected function findModel($params)
    {
        if (!empty($this->modelOptions->findParams))
            $params = array_merge($params, $this->modelOptions->findParams);
        $model = $this->modelClass;
        if (($model = $model::findOne($params)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    /**
     * @param $params
     * @return string|\yihai\core\db\ActiveRecord|null
     * @throws NotFoundHttpException
     */
    protected function findModelDelete($params)
    {
        if (!empty($this->modelOptions->mergeDeleteParams))
            $params = array_merge($params, $this->modelOptions->mergeDeleteParams);
        $model = $this->modelClass;
        if (($model = $model::findOne($params)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}