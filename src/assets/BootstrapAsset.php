<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use yii\web\AssetBundle;

class BootstrapAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/static/bootstrap';

    public $css = [
        'css/bootstrap.min.css'
    ];

    public $js = [
        'js/bootstrap.min.js'
    ];
    public $depends = [
        'yihai\core\assets\JqueryAsset'
    ];

}