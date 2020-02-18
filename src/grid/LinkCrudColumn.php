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
use yihai\core\db\ActiveRecord;
use yihai\core\helpers\Url;
use yihai\core\theming\Html;

class LinkCrudColumn extends Column
{
    /** @var ActiveRecord */
    public $crudModel;
    /** @var ModelOptions */
    private $crudModelOptions;
    public $crudLink;
    public $crudParams = [];
    public $template = '{add} {list}';
    public $buttons = [];
    public $buttonOptions = [];
    public $visibleButtons = [];
    public $urlCreator;
    public $headerOptions = ['class' => 'action-column text-center'];
    public $contentOptions = ['class' => 'text-center'];
    public $hideInAjax = true;

    public function init()
    {
        parent::init();
        $this->crudModelOptions = $this->crudModel->options();
        if($this->hideInAjax && ($this->crudModelOptions->actionDataList === Yihai::$app->controller->action->id)) {
            $this->visible = false;
            return;
        }
        $this->initButtons();
        if (is_string($this->crudLink))
            $this->crudLink = [$this->crudLink];
    }

    public function initButtons()
    {
        $this->initButton('add', 'plus');
        $this->initButton('list', 'list');
    }

    public function initButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'add':
                        $title = Yihai::t('yihai', 'Tambah');
                        break;
                    case 'list':
                        $title = Yihai::t('yihai', 'Data');
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title . ' (' . $this->header . ')',
                    'aria-label' => $title,
                    'data-pjax' => '0',
                    'data-toggle' => "modal",
                    'data-target' => '#yihai-crud-basemodal',
                    'data-padata-modal-type' => 'data'
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::icon($iconName);
                return Html::a($icon, $url, $options);
            };
        }
//        $this->buttons[$name] = Html::tag('a',$icon, [
//                'href' => Url::to($url),
//                'data-pjax' => 0,
//                'title' => Yihai::t('yihai-cat', 'Add Participant'),
//                'data-toggle' => "modal",
//                'data-target' => '#yihai-crud-basemodal',
//                'data-padata-modal-type' => 'data'
//            ]);
    }

    public function createUrl($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index, $this);
        }
        $crudModel = $this->crudModel;
        $classParam = null;
        $url = $this->crudLink;
        $url[0] = rtrim($url[0], '/');
        if ($action === 'list') {
            $url[0] = $url[0] . '/' . $this->crudModelOptions->actionDataList;
        } elseif ($action === 'add') {
            $url[0] = $url[0] . '/' . $this->crudModelOptions->actionCreate;
            $url['__redirect'] = Url::toRoute(['index']);
            $classParam = 'create-' . $crudModel::searchClassName();
        }

        if ($model instanceof ActiveRecord && isset($this->crudParams)) {
            if (is_array($this->crudParams) && isset($this->crudParams[$action])) {
                $url = $crudModel::buildSearchUrl($url, call_user_func($this->crudParams[$action], $crudModel, $model), $classParam);
            }
            elseif (is_callable($this->crudParams))
                $url = $crudModel::buildSearchUrl($url, call_user_func($this->crudParams, $crudModel, $model), $classParam);
            return $url;
        }
        return Url::to($url);
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];

            if (isset($this->visibleButtons[$name])) {
                $isVisible = $this->visibleButtons[$name] instanceof \Closure
                    ? call_user_func($this->visibleButtons[$name], $model, $key, $index)
                    : $this->visibleButtons[$name];
            } else {
                $isVisible = true;
            }
            if ($isVisible) {

            }
            if ($isVisible && isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);
                return call_user_func($this->buttons[$name], $url, $model, $key);
            }

            return '';
        }, $this->template);
    }

}