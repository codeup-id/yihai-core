<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


use yihai\core\assets\ICheckAsset;

class ICheck extends InputWidget
{
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';
    const SKIN_MINIMAL = 'minimal';
    const SKIN_FLAT = 'flat';
    const SKIN_FUTURICO = 'futurico';
    const SKIN_LINE = 'line';
    const SKIN_POLARIS = 'polaris';
    const SKIN_SQUARE = 'square';
    public $skin = self::SKIN_MINIMAL;
    public $type = self::TYPE_CHECKBOX;
    public $color;
    public function run()
    {
        if ($this->hasModel()) {
            if($this->type === self::TYPE_CHECKBOX)
                echo Html::activeCheckbox($this->model, $this->attribute, $this->options);
            elseif($this->type === self::TYPE_RADIO)
                echo Html::activeRadio($this->model, $this->attribute, $this->options);
        } else {
            if($this->type === self::TYPE_CHECKBOX)
                echo Html::checkbox($this->name, $this->value, $this->options);
            elseif($this->type === self::TYPE_RADIO)
                echo Html::radio($this->name, $this->value, $this->options);
        }
        $checkBoxSkin = 'icheckbox_'.$this->skin.($this->color? '-'.$this->color:'');
        $radioBoxSkin = 'iradio_'.$this->skin.($this->color? '-'.$this->color:'');
        $this->clientOptions['checkboxClass'] = $checkBoxSkin;
        $this->clientOptions['radioClass'] = $radioBoxSkin;
        $this->registerAsset(ICheckAsset::class)->css = [
            'skins/'.$this->skin.'/'.($this->color?$this->color.'.css':$this->skin.'.css')
        ];
        $this->registerJquery('iCheck');
    }
}