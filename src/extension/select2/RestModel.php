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
    private $options;
    /** @var Serializer */
    private $serializer;
    /** @var ActiveRecord */
    private $model;
    private $modelSearchClassName;
    /** @var string|ActiveRecord */
    public $modelClass;
    public $fields = '';
    public $perPage = 10;
    public $templateResult = 'return data.id;';
    public $templateSelection;
    public $filter = [];
    public $appendQuery = [];
    public $expands = [];
    public $useModelFilter = false;

    public function init()
    {
        parent::init();
        $this->model = new $this->modelClass();
        $model = $this->modelClass;
        $this->modelSearchClassName = $model::searchClassName();
        $this->serializer = Yihai::createObject($this->model->options()->restSerializer);
        if(!$this->templateSelection)
            $this->templateSelection = $this->templateResult;
    }

    public function getUrl()
    {
        $model = $this->modelClass;

        return Url::to([$model::crud_url_rest()]);
    }

    public function options()
    {
        $filter = Json::encode($this->filter);
        $appendQuery = Json::encode($this->appendQuery);
        $expands = implode(',', $this->expands);
        $filter_q = 'filter';
        if($this->useModelFilter)
            $filter_q = $this->modelSearchClassName;
        return [
            'ajax' => [
                'url' => $this->getUrl(),
                'cache'=>true,
                'data' => new JsExpression("function (params) {
                    var fields = '{$this->fields}';
                    var filter = {$filter};
                    
                    var query = {
                        'fields': fields,
                        'per-page': {$this->perPage},
                        'expand': '{$expands}',
                        page: params.page || 1
                    }
                    filter.forEach(function(v){
                        query['{$filter_q}['+v+']']  = params.term
                    })
                    return $.extend(query, {$appendQuery});
                }"),
                'processResults' => new JsExpression("function (data, params) {
                    return {
                      results: data.items,
                      pagination: {
                        more: data.{$this->serializer->linksEnvelope}.next
                      }
     
                    };
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