<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


use Yihai;
use yihai\core\grid\GridView;
use yihai\core\rbac\RbacHelper;
use yii\base\BaseObject;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/**
 * Class ModelOptions
 * @package yihai\core\base
 * @property callable $gridExportMpdf
 */
class ModelOptions extends BaseObject
{
    const _grid_show_column = '_grid_show_column';
    public $model = null;

    /**
     * params view yang di gunakan juga pada view di base crud view
     * @var array
     */
    public $viewParams = [
    ];
    /**
     * @var string
     */
    public $baseTitle = '';

    /**
     * @var null|\yihai\core\web\Controller
     */
    private $controller = null;

    private $controllerUniqueId;
    /**
     * @var string|bool
     */
    public $actionIndex = 'index';
    /**
     * @var string|bool
     */
    public $actionCreate = 'create';
    /**
     * @var string|bool
     */
    public $actionUpdate = 'update';
    /**
     * @var string|bool
     */
    public $actionDelete = 'delete';
    /**
     * @var string|bool
     */
    public $actionImport = 'import';
    /**
     * @var string|bool
     */
    public $actionExport = 'export';
    /**
     * @var string|bool
     */
    public $actionExportGrid = 'export-grid';
    /**
     * @var string|bool
     */
    public $actionView = 'view';

    /**
     * @var string|bool
     */
    public $actionDataList = '_data_list';

    public $actionRest = [
        'index', 'create', 'update', 'delete', 'view'
    ];

    public $redirect;
    public $viewFileIndex = '@yihai/views/_pages/crud-index';
    public $viewFileGridExport = '@yihai/views/_pages/crud-grid-export';
    public $viewFileForm = '@yihai/views/_pages/crud-form';
    public $viewFileView = '@yihai/views/_pages/crud-view';
    public $viewFileImport = '@yihai/views/_pages/crud-import';
    public $viewFileExport = '@yihai/views/_pages/crud-export';

    public $insertMultiple = true;
    public $insertMultipleCount = 1;
    public $templateLinkCreate = "<a href=\"{url}\" {modal}>{label}</a>";


    /**
     * menggunakan modal pada link create
     * @var bool
     */
    public $useModalLinkCreate = true;
    /**
     * menggunakan modal pada link update
     * @var bool
     */
    public $useModalLinkUpdate = true;
    /**
     * menggunakan modal pada link import
     * @var bool
     */
    public $useModalLinkImport = true;
    /**
     * menggunakan modal pada link delete
     * @var bool
     */
    public $useModalLinkDelete = true;
    /**
     * menggunakan modal pada link export
     * @var bool
     */
    public $useModalLinkExport = true;
    /**
     * menggunakan modal pada link view
     * @var bool
     */
    public $useModalLinkView = true;

    /** GRID CONFIG */
    public $gridPrint = true;
    public $gridPdf = true;
    public $gridXlsx = true;
    public $gridCsv = true;
    public $gridHtml = true;

    public $gridPdfOrientation = 'P';
    public $gridPdfSize = 'A4';


    /**
     * menampilkan tombol update/save dan lanjutkan edit pada form
     * @var bool
     */
    public $formButtonContinueEdit = false;
    /**
     * @var array|GridView
     */
    public $gridViewConfig = [];
    /**
     * @var ActiveDataProvider|array
     */
    public $gridDataProvider = [];
    /**
     * @var array
     */
    public $gridColumnData = [];
    /**
     * @var bool|array|\yii\grid\SerialColumn
     */
    public $gridViewSerialColumn = [
        'class' => '\yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'width: 1px;text-align:center'],
        'contentOptions' => ['style' => 'width: 1px;text-align:center']
    ];
    /**
     * class config action column
     * @var bool|array|\yihai\core\grid\ActionColumn
     */
    public $gridViewActionColumn = [
        'class' => '\yihai\core\grid\ActionColumn',
    ];

    /**
     * @var bool|array|\yihai\core\grid\CheckboxColumn
     */
    public $gridViewCheckboxColumn = [
        'class' => '\yihai\core\grid\CheckboxColumn'
    ];

    public $actionLinkUpdateModal = true;

    /**
     * hint/info yang akan tampil pada main crud
     * @var array
     */
    public $hint = [];
    /**
     * diset hanya pada crud action
     * @var string
     */
    public $formType;

    /**
     * ActiveForm config untuk merge
     * @var array|\yihai\core\theming\ActiveForm
     */
    public $formConfig = [];

    /**
     * akan di merge pada findModel
     * @var array
     */
    public $findParams = [];

    public $mergeDeleteParams = [];
    /**
     * attribut untuk detailview yang akan ditampilkan pada action view
     * @var array
     */
    public $detailViewData = [];
    /**
     * attribute untuk info created updated, muncul di action view
     * jika TRUE = maka tampil semua info created_by,created_at,updated_by,updated_at
     * atau array, ['created_by',...]
     * @var bool|array
     */
    public $detailViewCreatedUpdated = false;

    /**
     * custom data, format:
     * [ {label} => [{attribute1},{attribute2}] ]
     * @var bool|array
     */
    public $detailViewCustom = [];

    public $importMax = 100;
    public $importAttributes = [];
    public $importCustom = [];
    public $importRefs = [];
    public $importInfo = [];


    public $exportAttributes = [];
    public $exportAttributesFormat = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public $restSerializer = [
        'class' => 'yihai\core\rest\Serializer',
        'collectionEnvelope' => 'items'
    ];
    public $restDataFilter = [
        'class' => 'yii\data\ActiveDataFilter',
    ];

    private $_gridExportMpdf;

    /**
     * ex: [
     *  function($modelOptions){ return '.......'; }
     * ]
     * @var array append links pada button di index
     */
    public $appendLinks = [];
    public function init()
    {
        parent::init();
    }

    /**
     * @param $action
     * @return bool
     */
    public function userCanAction($action)
    {
        $id = $this->getControllerUniqueId();
        $id_action = $id . '/' . $this->getActionId($action);
        return RbacHelper::checkUserCanMenu($id_action);
    }

    public function getActionId($action = '')
    {
        if ($action === '')
            return $this->controller->action->id;
        switch ($action) {
            case 'create':
                $action_id = $this->actionCreate;
                break;
            case 'update':
                $action_id = $this->actionUpdate;
                break;
            case 'delete':
                $action_id = $this->actionDelete;
                break;
            case 'view':
                $action_id = $this->actionView;
                break;
            case 'import':
                $action_id = $this->actionImport;
                break;
            case 'export':
                $action_id = $this->actionExport;
                break;
            default:
                $action_id = $action;
        }
        return $action_id;
    }

    public function getActionUrl($action = '')
    {
        $id = $this->getControllerUniqueId();
        $action = $this->getActionId($action);
        return '/' . $id . '/' . $action;

    }

    public function getActionUrlTo($action = '', $params = [])
    {
        $url = [$this->getActionUrl($action)];
        $url = array_merge($url, $params);
        return Url::to($url);

    }

    /**
     * @return \yihai\core\web\Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param \yihai\core\web\Controller $controller
     */
    public function setController($controller)
    {
        $this->controllerUniqueId = $controller->getUniqueId();
        $this->controller = $controller;
    }

    /**
     * @return array|bool
     */
    public function getGridViewActionColumn()
    {
        if ($this->gridViewActionColumn === false) return false;
        $this->gridViewActionColumn['modelOptions'] = $this;
        return $this->gridViewActionColumn;
    }

    /**
     * @return string
     */
    public function getControllerUniqueId()
    {
        return $this->controllerUniqueId;
    }

    /**
     * @return string
     */
    public function getBaseTitle()
    {
        if (!$this->baseTitle)
            $this->baseTitle = $this->getControllerUniqueId();
        return $this->baseTitle;
    }

    /**
     * @return array|bool
     */
    public function getGridViewSerialColumn()
    {
        return $this->gridViewSerialColumn;
    }

    /**
     * @return array|bool
     */
    public function getGridViewCheckboxColumn()
    {
        if (is_array($this->gridViewCheckboxColumn))
            $this->gridViewCheckboxColumn['modelOptions'] = $this;
        return $this->gridViewCheckboxColumn;
    }

    public function gridColumnData()
    {
        if($show_column = Yihai::$app->request->get(static::_grid_show_column)){
            $show_column=explode(',',$show_column);
            foreach($this->gridColumnData as $i => $config){
                if(is_array($config) && isset($config['attribute']) && !in_array($config['attribute'], $show_column)){
                    unset($this->gridColumnData[$i]);
                }elseif(is_string($config) && !in_array($config, $show_column)){
                    unset($this->gridColumnData[$i]);
                }
            }
        }
        return $this->gridColumnData;
    }

    /**
     * @return array
     */
    public function getImportAttributes()
    {
        $importAttributes = [];
        foreach ($this->importAttributes as $attribute) {
            if (is_string($attribute)) {
                $importAttributes[] = [
                    'data' => $attribute,
                    'label' => $attribute,
                    'attribute' => $attribute,
                ];
            } elseif (is_array($attribute)) {
                if (!isset($attribute['attribute']))
                    continue;
                if (!isset($attribute['label']))
                    $attribute['label'] = $attribute['attribute'];
                if (!isset($attribute['data']))
                    $attribute['data'] = $attribute['attribute'];
                $importAttributes[] = $attribute;
            }
        }
        return $importAttributes;
    }

    /**
     * @return callable
     */
    public function getGridExportMpdf()
    {
        return $this->_gridExportMpdf;
    }

    /**
     * function($mpdf){...}
     * @param callable $gridExportMpdf
     */
    public function setGridExportMpdf($gridExportMpdf)
    {
        $this->_gridExportMpdf = $gridExportMpdf;
    }

}