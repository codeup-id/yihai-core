<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


use yii\helpers\Html;

class Input extends BaseWidget
{
    public static $autoIdPrefix = 'input-w-';
    public $type = 'text';
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, ['widget' => 'btn']);
        $this->options['type'] = $this->type;
        $this->clientEvents['click'] = 'function(){alert(/1/)}';
        $this->registerClientEvents();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        return Html::tag('input', 'ADA', $this->options);
    }
}