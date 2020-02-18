<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\select2;


use Yihai;
use yihai\core\db\ActiveRecord;
use yihai\core\helpers\Url;
use yii\base\BaseObject;
use yii\data\ActiveDataFilter;
use yii\helpers\Json;
use yii\rest\Serializer;
use yii\web\JsExpression;

class RestModel extends BaseObject
{
    /**
     * Jika url di set, maka tidak akan menggunakan model
     * @var string
     */
    public $url;
    /** @var Serializer */
    private $serializer;
    /** @var ActiveRecord */
    private $model;

    /**
     * key untuk filter, jika menggunakan url maka harus di set manual
     * @var string
     */
    public $modelSearchClassName;
    /** @var string|ActiveRecord */
    public $modelClass;
    /**
     * fields yang akan di ambil
     * @var string
     */
    public $fields = '';
    /**
     * data per request
     * @var int
     */
    public $perPage = 10;
    /**
     * template data yang akan ditampilkan
     * @var string
     */
    public $templateResult = 'return data.id;';
    /**
     * data/field yang akan ditampilkan setelah dipilih. jika kosong, maka default adalah $templateResult
     * @var string
     */
    public $templateSelection;

    /**
     * data yang akan di filter (menggunakan ModelSearch), contoh
     * ```php
     *  ['id','uid']
     * ```
     * @var array
     * @see ActiveRecord::prosesFiltering()
     */
    public $filter = [];

    /**
     * menggunakan DataFilter
     * ```php
     *  [
     *      'or',
     *      ['LIKE', 'nama', '{{term}}'],
     *      ['LIKE', 'id', '{{term}}'],
     *  ]
     * @var array
     * @see ActiveRecord::prosesFiltering()
     */
    public $filterData = [];

    /**
     * Custom query params
     * @var array
     */
    public $queryParams = [];
    /**
     * data yang akan di tambah pada query. berbeda dengan $filter, ini dapat melakukan filter kustom.
     * ```php
     *  [
     *      'field1' => new JsExpression("function(){return $('#id-input').val();}"),
     *      'field2' => ....
     *  ]
     *
     * @var array
     */
    public $appendQuery = [];
    /**
     * data relasi model yang akan digunakan.
     * @var array
     */
    public $expands = [];
    /**
     * jika "true" maka query pada filter menggunakan nama class model,
     * jika "false" maka query filter menggunakan "filter".
     * @var bool
     */
    public $useModelFilter = false;

    /**
     * digunakan untuk mengganti value "id" yang akan disimpan pada inputan.
     * ```php
     *  $dataId = new JsExpression("function(data){return data.uid;}")
     *```
     * @var string
     */
    public $dataID = 'null';
    public function init()
    {
        parent::init();
        if($this->url){
            $this->serializer = Yihai::createObject([
                'class' => 'yii\rest\Serializer',
                'collectionEnvelope' => 'items'
            ]);
        }else {
            $this->model = new $this->modelClass();
            $model = $this->modelClass;
            $this->modelSearchClassName = $model::searchClassName();
            $this->serializer = Yihai::createObject($this->model->options()->restSerializer);
        }
        if(!$this->templateSelection)
            $this->templateSelection = $this->templateResult;
    }

    public function getUrl()
    {
        if($this->url)
            return Url::to($this->url);

        $model = $this->modelClass;
        return Url::to([$model::crud_url_rest()]);
    }

    public function options()
    {
        $filter = Json::encode($this->filter);
        $filterData = Json::encode($this->filterData);
        $queryParams = Json::encode($this->queryParams);
        $appendQuery = Json::encode($this->appendQuery);
        $expands = implode(',', $this->expands);
        $filter_q = 'filter';
        if($this->useModelFilter)
            $filter_q = $this->modelSearchClassName;
        return [
            'ajax' => [
                'url' => $this->getUrl(),
                'type'=>'POST',
                'contentType'=>'application/json',
                'dataType' => 'json',
                'cache'=>true,
                'data' => new JsExpression("function (params) {
                    var fields = '{$this->fields}';
                    var filter = {$filter};
                    var filterData = {$filterData};
                    var queryParams = {$queryParams};
                    var appendQuery = {$appendQuery};
                    
                    var query = {
                        'fields': fields,
                        'per-page': {$this->perPage},
                        'expand': '{$expands}',
                        page: params.page || 1
                    }
                    if(!params.term) params.term = '';
                    if(filter.length){
                        if(!query['{$filter_q}'])
                            query['{$filter_q}'] = {};
                        filter.forEach(function(v){
                            query['{$filter_q}'][v] = params.term
                        })
                        $.each(appendQuery, function(k,v){
                            query['{$filter_q}'][k] = v
                        })
                    }else if(filterData.length){
                        query['filter'] = filterData; 
                    }
                    $.each(queryParams, function(k,v){
                        query[k] = v
                    })
                    
                    return JSON.stringify(query).replace(/{{term}}/g, params.term);
                }"),
                'processResults' => new JsExpression("function (_data, params) {
                    var dataId = {$this->dataID};
                    if(_data.items && dataId !== null){
                        var data_ = $.map(_data.items, function(obj){
                            obj.id = dataId(obj)
                            return obj;
                        });
                    }
                    return {
                      results: data_ || _data.items,
                      pagination: {
                        more: _data.{$this->serializer->linksEnvelope}.next
                      }
     
                    };
                }"),
                'results' => new JsExpression("function(data, page){
                    var data = $.map(data, function (obj) {
                        obj.id = obj.uid || obj.id;
                        return obj;
                        });
                }")
            ],
            'templateResult' => new JsExpression("function(data) {
                if(data.loading)
                    return data.text;
                {$this->templateResult}
            }"),
            'templateSelection' => new JsExpression("function (data) {
                {$this->templateSelection}
            }"),
        ];
    }

}