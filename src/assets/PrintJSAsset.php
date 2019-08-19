<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use yii\web\AssetBundle;

class PrintJSAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/static/printjs';
    public $js = [
        'print.min.js'
    ];
    public $css = [
        'print.min.css'
    ];
    public $depends = [
        'yihai\core\assets\JqueryAsset'
    ];

}