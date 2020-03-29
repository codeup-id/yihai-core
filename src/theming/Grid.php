<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


class Grid extends BaseWidget
{
    public function init()
    {
        parent::init();
        if(isset($this->options['class']))
            $this->options['class'] = 'row '.$this->options['class'];
        else{
            $this->options['class'] = 'row';
        }
        echo Html::beginTag('div', $this->options);
    }

    public function run()
    {

        echo Html::endTag('div');

    }

    public function beginCol($cols = [], $options = [])
    {
        $class = [];
        foreach($cols as $col){
            $class[] = 'col-'.$col;
        }
//        $options['class'] = $class;
        Html::addCssClass($options, $class);
        echo Html::beginTag('div', $options);
    }

    public function endCol()
    {
        echo Html::endTag('div');
    }
}