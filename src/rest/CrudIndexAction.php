<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\rest;


use Yihai;
use yihai\core\base\FilterModel;
use Yii;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\data\DataFilter;

class CrudIndexAction extends CrudAction
{

    /**
     * @var DataFilter|null data filter to be used for the search filter composition.
     */
    public $dataFilter;


    /**
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        return $this->prepareDataProvider();
    }

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    protected function prepareDataProvider()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }
        $dataProvider = Yii::createObject([
            'class' => ActiveDataProvider::class,
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ]);
        if ($this->model->hasMethod('initDataProvider')) {
            $this->model->initDataProvider($dataProvider);
        } else {
            $modelClass = $this->modelClass;
            $dataProvider->query = $modelClass::find();
        }
        return $dataProvider;
    }
}