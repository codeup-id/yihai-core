<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\select2;

use yihai\core\base\ModelOptions;
use yihai\core\theming\Html;
use yihai\core\theming\InputWidget;
use yii\helpers\ArrayHelper;

class Select2 extends InputWidget
{

    public $items = [];
    public $crudRest = [];
    public $crudRestFields = [];
    /** @var ModelOptions */
    public $modelOptions;
    /** @var array|RestModel */
    public $restModel;

    // EVENTS
    /** @var string  function(e){...} */
    public $onSelect;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if($this->restModel && is_array($this->restModel)){
            $this->restModel['class'] = RestModel::class;
            $this->restModel = \Yihai::createObject($this->restModel);
        }
        if($this->restModel && !$this->restModel instanceof RestModel)
            $this->restModel = false;
        if(!$this->restModel && !isset($this->clientOptions['multiple']))
            $this->clientOptions['placeholder'] = '-----';
        $this->initPlaceholder();
    }
    /**
     * Select2 plugin placeholder check and initialization
     */
    protected function initPlaceholder()
    {
        $multipleSelection = ArrayHelper::getValue($this->options, 'multiple');
        if (!empty($this->options['prompt']) && empty($this->clientOptions['placeholder'])) {
            $this->clientOptions['placeholder'] = $multipleSelection
                ? ArrayHelper::remove($this->options, 'prompt')
                : $this->options['prompt'];
            return null;
        } elseif (!empty($this->options['placeholder'])) {
            $this->clientOptions['placeholder'] = ArrayHelper::remove($this->options, 'placeholder');
        }
        if (!empty($this->clientOptions['placeholder']) && !$multipleSelection) {
            $this->options['prompt'] = is_string($this->clientOptions['placeholder'])
                ? $this->clientOptions['placeholder']
                : ArrayHelper::getValue((array)$this->clientOptions['placeholder'], 'placeholder', '');
        }
    }
    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            if(isset($this->model->{$this->attribute}) && $this->model->{$this->attribute}){
                $this->value = $this->model->{$this->attribute};
            }
            echo Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        } else {
            echo Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }
        if($this->restModel){
            $this->clientOptions = ArrayHelper::merge($this->restModel->options(), $this->clientOptions, $this->clientOptions);
        }
        if($this->onSelect)
            $this->clientEvents['select2:select'] = $this->onSelect;

        $this->registerAsset(Select2Asset::class);
        $this->registerJquery('select2');
//        $this->getView()->registerJs('$.fn.modal.Constructor.prototype.enforceFocus = function() {};');
    }
}