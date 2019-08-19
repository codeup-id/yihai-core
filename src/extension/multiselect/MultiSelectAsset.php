<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\multiselect;


use yii\web\AssetBundle;

class MultiSelectAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/assets';
    public $js = [
        'bootstrap-multiselect.js'
    ];
    public $css = [
        'bootstrap-multiselect.css'
    ];
    public $depends = [
        'yihai\core\assets\JqueryAsset',
        'yihai\core\assets\BootstrapAsset'
    ];
}