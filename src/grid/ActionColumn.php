<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\grid;


use Yihai;
use yihai\core\base\ModelOptions;
use yihai\core\theming\Html;
use yii\helpers\ArrayHelper;

class ActionColumn extends \yii\grid\ActionColumn
{
    /** @var bool jika false maka tidak akan menggunakan modal untuk form */
    public $useModal = true;
    /**
     * @var ModelOptions
     */
    public $modelOptions;
    /** @var array query params pada url */
    public $queryParams = [];

    public $templateAppend;

    public $buttonsCustom = [];

    public function init()
    {
        if (!$this->header)
            $this->header = Yihai::t('yihai', 'Action');
        if ($this->headerOptions)
            $this->headerOptions = ['class' => 'text-center'];
        if (!$this->contentOptions)
            $this->contentOptions = ['class' => 'text-center'];
        if ($this->templateAppend)
            $this->template .= $this->templateAppend;
        if ($this->buttonsCustom) {
            foreach ($this->buttonsCustom as $name => $v) {
                if (!isset($v['name'])) $v['name'] = $name;
                $this->buttonCustom($v);
            }
        }
        parent::init();

    }

    /**
     * @param string $action
     * @param \yii\db\ActiveRecordInterface $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function createUrl($action, $model, $key, $index)
    {
        if ($this->modelOptions)
            $action = $this->modelOptions->getActionId($action);
        if (!empty($this->queryParams)) {
            $key = [];
            foreach ($this->queryParams as $k) {
                $key[$k] = $model->{$k};
            }
        } else {
            $key = $model->getPrimaryKey(true);
        }
        return parent::createUrl($action, $model, $key, $index);
    }

    /**
     * @inheritDoc
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                $classColor = '';
                switch ($name) {
                    case 'view':
                        $title = Yihai::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Yihai::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yihai::t('yii', 'Delete');
                        $classColor = 'text-danger ';
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::icon($iconName, ['class' => $classColor, 'tag' => 'i']);
//                $icon = Html::tag('i', '', ['class' => $classColor . "far fa-$iconName"]);
                return Html::a($icon, $url, $options);
            };
        }
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if ($this->modelOptions->actionView && $this->modelOptions->userCanAction('view'))
            $this->initDefaultButton('view', 'eye', ($this->useModal && $this->modelOptions->useModalLinkView ? ['data-toggle' => 'modal', 'data-target' => '#yihai-crud-basemodal', 'data-modal-type' => 'view'] : []));

        if ($this->modelOptions->actionUpdate && $this->modelOptions->userCanAction('update'))
            $this->initDefaultButton('update', 'pencil', ($this->useModal && $this->modelOptions->useModalLinkUpdate ? ['data-toggle' => 'modal', 'data-target' => '#yihai-crud-basemodal', 'data-modal-type' => 'update'] : []));

        if ($this->modelOptions->actionDelete && $this->modelOptions->userCanAction('delete'))
            $this->initDefaultButton('delete', 'trash', ($this->useModal ? ['data-toggle' => 'modal', 'data-target' => '#yihai-crud-basemodal-delete', 'data-modal-type' => 'update'] : []));

    }

    protected function buttonCustom($options = [])
    {
        $modal = ArrayHelper::remove($options, 'modal', true);
        $name = ArrayHelper::remove($options, 'name');
        $iconName = ArrayHelper::remove($options, 'icon');
        if ($modal) {
            $options = array_merge(['data-toggle' => 'modal', 'data-target' => '#yihai-crud-basemodal', 'data-modal-type' => 'custom'], $options);
        }
        if ($this->modelOptions->userCanAction($name)) {
            $this->initDefaultButton($name, $iconName, $options);
        }

    }
}