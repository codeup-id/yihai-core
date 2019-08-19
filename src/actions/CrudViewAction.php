<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\actions;


use Yihai;
use yii\base\InvalidConfigException;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\widgets\ListView;

class CrudViewAction extends CrudAction
{
    protected $type = self::TYPE_VIEW;

    public $viewFile;
    /** @var ListView */
    public $listView = [];
    /** @var array|DetailView */
    private $detailViewData = [];
    private $detailViewCreatedUpdated = [];
    private $detailViewCustom = [];
    /** @var array|DetailView */
    public $detailViewOptions = [];

    /**
     * @var array attribut data detail
     */
    public $attributes;

    /**
     * attribute untuk info created updated, muncul di action view
     * jika TRUE = maka tampil semua info created_by,created_at,updated_by,updated_at
     * atau array, ['created_by',...]
     * @var bool|array
     */
    public $createdUpdated = false;
    /**
     * custom data, format:
     * [ {label} => [{attribute1},{attribute2}] ]
     * @var bool|array
     */
    public $customDetailView = false;

    public function init()
    {
        if ($this->viewFile)
            $this->modelOptions->viewFileView = $this->viewFile;
        parent::init();
        if ($this->attributes)
            $this->modelOptions->detailViewData = $this->attributes;
        if ($this->createdUpdated)
            $this->modelOptions->detailViewCreatedUpdated = $this->createdUpdated;
        if ($this->customDetailView)
            $this->modelOptions->detailViewCustom = $this->customDetailView;
    }

    public function run()
    {
        if (empty($this->modelOptions->detailViewData)) {
            $this->addParams('noData', true);
        } else {
            $this->addParams('noData', false);
            $this->detailViewData = DetailView::widget(ArrayHelper::merge([
                'id' => 'yihai-detailviewdata-' . $this->modelOptions->getActionId(),
                'model' => $this->model,
                'attributes' => $this->modelOptions->detailViewData
            ], $this->detailViewOptions));
        }
        $this->initDetailViewCreatedUpdated();
        $this->initDetailViewCustom();
        $this->addParams([
            'listView' => $this->listView,
            'detailViewData' => $this->detailViewData,
        ]);
        return parent::run();
    }

    private function initDetailViewCreatedUpdated()
    {
        if ($this->modelOptions->detailViewCreatedUpdated === false)
            return;
        $attribute = [];
        if (is_array($this->modelOptions->detailViewCreatedUpdated))
            $attribute = $this->modelOptions->detailViewCreatedUpdated;
        elseif (is_bool($this->modelOptions->detailViewCreatedUpdated))
            $attribute = [
                [
                    'attribute' => 'created_by_user.username',
                    'label' => Yihai::t('yihai', 'Created By')
                ],
                'created_at:datetime',
                [
                    'attribute' => 'updated_by_user.username',
                    'label' => Yihai::t('yihai', 'Updated By')
                ],
                'updated_at:datetime'
            ];

        $this->detailViewCreatedUpdated = DetailView::widget(ArrayHelper::merge([
            'id' => 'yihai-detailviewcreatedupdated-' . $this->modelOptions->getActionId(),
            'model' => $this->model,
            'attributes' => $attribute
        ], $this->detailViewOptions));
        $this->addParams('detailViewCreatedUpdated', $this->detailViewCreatedUpdated);

    }

    private function initDetailViewCustom()
    {
        foreach ($this->modelOptions->detailViewCustom as $i => $attribute) {
            if (is_callable($attribute)) {
                $custom = call_user_func($attribute, $this->model);
                foreach ($custom as $label => $config) {
                    $this->detailViewCustom[Yihai::t('yihai', ucfirst($label))] = DetailView::widget(ArrayHelper::merge($config, $this->detailViewOptions));
                }
            } else {
                $this->detailViewCustom[Yihai::t('yihai', ucfirst($i))] = DetailView::widget(ArrayHelper::merge([
                    'id' => 'yihai-detailviewcustom-' . md5($i) . '-' . str_replace('/','-',$this->modelOptions->getActionUrl()),
                    'model' => $this->model,
                    'attributes' => $attribute
                ], $this->detailViewOptions));
            }
        }
        $this->addParams('detailViewCustom', $this->detailViewCustom);

    }
}