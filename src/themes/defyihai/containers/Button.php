<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\themes\defyihai\containers;


use yii\base\Widget;
use yii\helpers\Html;

class  Button extends \yihai\core\theming\Button
{
    public function run()
    {
        return Html::tag('button', 'ADA---theme', $this->options);
    }

}