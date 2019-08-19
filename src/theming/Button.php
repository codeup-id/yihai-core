<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


class Button extends BaseWidget
{
    const SIZE_XS = 'xs';
    const SIZE_SM = 'sm';
    const SIZE_MD = 'md';
    const SIZE_LG = 'lg';
    /**
     * @var string the tag to use to render the button
     */
    public $tag = 'button';
    /**
     * @var string the button label
     */
    public $label = 'Button';
    /**
     * @var bool whether the label should be HTML-encoded.
     */
    public $encodeLabel = true;
    public $type = 'default';
    public $size = false;

    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, ['widget' => 'btn']);
        if ($this->type)
            Html::addCssClass($this->options, 'btn-' . $this->type);
        if ($this->size)
            Html::addCssClass($this->options, 'btn-'.$this->size);
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $this->registerClientEvents();
        return Html::tag($this->tag, $this->encodeLabel ? Html::encode($this->label) : $this->label, $this->options);
    }

}