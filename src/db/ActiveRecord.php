<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\db;

use Yihai;
use yihai\core\base\Model;
use yihai\core\base\ModelOptions;
use yihai\core\base\FilterModel;
use yihai\core\helpers\Url;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataFilter;
use yii\helpers\ArrayHelper;

class ActiveRecord extends \yii\db\ActiveRecord
{
    public static $crud_url;
    private $_options;
    private $_yihai_scenarios = [];

    const SCENARIO_CREATE = Model::SCENARIO_CREATE;
    const SCENARIO_UPDATE = Model::SCENARIO_UPDATE;
    /**
     * @var FilterModel
     */
    private $_filterModel;

    /** @var ActiveDataFilter */
    public $dataFilter;
    public function init()
    {
        parent::init();
        if (!empty($this->filterRules())) {
            $filterModel = FilterModel::newFromRules($this->filterRules());
            $filterModel->setFormName($this->_searchClassName());
            $this->_filterModel = $filterModel;
        }
        if(!$this->dataFilter){
            $this->dataFilter = new ActiveDataFilter([
                'searchModel' => $this->_filterModel
            ]);
        }
    }

    /**
     * set options
     * @return ModelOptions
     */
    protected function _options()
    {
        return new ModelOptions();
    }

    /**
     * set options
     * @return ModelOptions
     */
    public function options()
    {
        if (!$this->_options)
            $this->_options = $this->_options();
        return $this->_options;
    }

    public static function className()
    {
        return static::class;
    }

    public static function classNameSort()
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }

    public static function searchClassName()
    {
        $path = explode('\\', static::class);
        return array_pop($path) . 'Search';
    }

    protected function _searchClassName()
    {
        $path = explode('\\', static::class);
        return array_pop($path) . 'Search';
    }

    public static function buildSearchUrl($url, $fields = [], $cls = null)
    {
        if ($cls === null) {
            $path = explode('\\', static::class);
            $class = array_pop($path) . 'Search';
        } else {
            $class = $cls;
        }
        $query = [
            $class => []
        ];
        foreach ($fields as $key => $val) {
            $query[$class][$key] = $val;
        }
        if (!is_array($url))
            $url = [$url];
        $url = array_merge($url, $query);
        return Url::to($url);
    }

    public function scenarios()
    {
        return array_merge($this->_yihai_scenarios, parent::scenarios());
    }

    /**
     * @param $name
     * @param array $attributes
     */
    public function addScenario($name, $attributes = [])
    {
        $scenarios = parent::scenarios();
        if (empty($attributes)) {
            if (isset($scenarios[$name]))
                $attributes = $scenarios[$name];
            elseif (isset($scenarios[self::SCENARIO_DEFAULT])) {
                $attributes = $scenarios[self::SCENARIO_DEFAULT];
            }
        }
        $this->_yihai_scenarios[$name] = $attributes;
    }

    /**
     * rules untuk search
     * @return array
     */
    public function filterRules()
    {
        return [];
    }

    /**
     * @param $dataProvider \yii\data\ActiveDataProvider
     */
    public function searchDataProvider(&$dataProvider)
    {

    }

    /**
     * @param \yii\db\QueryInterface|\yii\db\ActiveQuery $query
     * @param FilterModel|static $filterModel
     * @return void
     */
    public function onSearch(&$query, $filterModel)
    {

    }

    public function getFilterModel()
    {
        return $this->_filterModel;
    }


    /**
     * @param \yii\data\ActiveDataProvider $dataProvider
     * @throws InvalidConfigException
     */
    public function prosesFilteringDataFilter(&$dataProvider)
    {
        $requestParams = Yihai::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yihai::$app->getRequest()->getQueryParams();
        }
        unset($requestParams['restAction']);
        if ($this->dataFilter->load($requestParams) && ($dataFilterBuild = $this->dataFilter->build())) {
            $dataProvider->query->filterWhere($dataFilterBuild);
        }
    }

    /**
     * @param \yii\data\ActiveDataProvider $dataProvider
     */
    protected function prosesFiltering(&$dataProvider)
    {
        if ($this->_filterModel === null)
            return;

        try {
            $params = Yihai::$app->request->getQueryParams();
            if ($this->_filterModel->load($params, $this->_searchClassName()) && $this->_filterModel->validate()) {
                $this->onSearch($dataProvider->query, $this->_filterModel);
            }
        } catch (InvalidConfigException $e) {
        }
    }

    /**
     * @param \yii\data\ActiveDataProvider $dataProvider
     */
    protected function onDataProvider(&$dataProvider)
    {
    }

    /**
     * @param \yii\data\ActiveDataProvider $dataProvider
     * @throws InvalidConfigException
     */
    public function initDataProvider(&$dataProvider)
    {
        $this->onDataProvider($dataProvider);
        if (!$dataProvider->query) {
            $query = static::find();
            $dataProvider->query = $query;
        }

        $this->prosesFilteringDataFilter($dataProvider);
        $this->prosesFiltering($dataProvider);

    }

    protected function addSortAttribute($attribute)
    {
        return [
            'asc' => [$attribute => SORT_ASC],
            'desc' => [$attribute => SORT_DESC]
        ];
    }

    public static function toArrayDropdown($key_field, $value_vield, $query = null)
    {
        if (!$query)
            $query = static::find();
        return ArrayHelper::map($query->all(), $key_field, $value_vield);
    }

    /**
     * @param $path
     * @param $value
     */
    public function setProperty($path, $value)
    {

        if ($path === null) {
            return;
        }

        $keys = is_array($path) ? $path : explode('.', $path);
        if (isset($keys[0]) && $this->canSetProperty($keys[0])) {
            if (count($keys) === 1) {
                $this->{$keys[0]} = $value;
            } else {
                $top = $keys[0];
//                unset($keys[0]);
                if (!$this->{$top})
                    $this->{$top} = [];
                ArrayHelper::setValue($this, implode('.', $keys), $value);
            }

        }
    }

    public function queryDefaultValue($attribute)
    {
        $t = $this->getIsNewRecord() ? 'create' : 'update';
        $defaultValueQuery = $t . '-' . static::searchClassName();
        if ($query = Yihai::$app->getRequest()->getQueryParam($defaultValueQuery)) {
            if (isset($query[$attribute])) {
                $this->{$attribute} = $query[$attribute];
                return $query[$attribute];
            }
        }
        return null;

    }

    /**
     * @param string $path
     * @return string url
     */
    public static function crud_url($path = '')
    {
        if (!static::$crud_url) {
            return '';
        }
        $url = '/' . trim(static::$crud_url, '/') . '/';
        if ($path)
            $url = $url . $path;
        return $url;
    }

    /**
     * Dipakai pada rest select2
     * @param string $path
     * @return string url
     */
    public static function crud_url_rest($path = 'index')
    {
        return static::crud_url('__rest/' . $path);
    }

    public static function reportFields()
    {
        return (new static())->attributes;
    }
}