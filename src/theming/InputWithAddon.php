<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


class InputWithAddon extends InputWidget
{
    public $type = 'text';
    public $addonTag = 'span';
    public $addonText = '@';
    public $addonOptions = [];
    /** @var string left|right */
    public $addonPosition = 'right';
    public $groupOptions = [];
    public function run()
    {
        Html::addCssClass($this->options, ['form-control']);
        Html::addCssClass($this->groupOptions,['input-group']);
        Html::addCssClass($this->addonOptions,['input-group-addon']);
        echo Html::beginTag('div', $this->groupOptions);
        $addon = Html::tag($this->addonTag, $this->addonText, $this->addonOptions);
        if($this->addonPosition === 'left')
            echo $addon;
        if($this->hasModel()){
            echo Html::activeInput($this->type, $this->model, $this->attribute, $this->options);
        }else{
            echo Html::input($this->type, $this->name, $this->value, $this->options);
        }
        if($this->addonPosition === 'right')
            echo $addon;
        echo Html::endTag('div');

    }

}