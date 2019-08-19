<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\multiselect;


use yihai\core\theming\Html;
use yii\base\InvalidConfigException;
use yihai\core\theming\InputWidget;

class MultiSelect extends InputWidget
{
    /**
     * @var array data for generating the list options (value=>display)
     */
    public $items = [];
    public $multiple = true;
    public $numberDisplayed = 2;
    public $includeSelectAllOption = true;
    public $selectAll = false;

    /**
     * Initializes the widget.
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (empty($this->items)) {
            throw new  InvalidConfigException('"Multiselect::$data" attribute cannot be blank or an empty array.');
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->buildDefaults();
        if ($this->hasModel()) {
            echo Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        } else {
            echo Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }
        $this->registerAsset(MultiSelectAsset::class);
        $this->registerJquery('multiselect');
    }

    protected function buildDefaults()
    {
        if ($this->multiple) {
            $this->options['multiple'] = 'multiple';
        }
        if ($this->includeSelectAllOption)
            $this->clientOptions['includeSelectAllOption'] = true;

        $this->clientOptions['numberDisplayed'] = $this->numberDisplayed;
    }

}