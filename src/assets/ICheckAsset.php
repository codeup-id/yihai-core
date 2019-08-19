<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use yii\web\AssetBundle;

class ICheckAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/static/icheck';
    public $js = [
        'icheck.min.js'
    ];
    public $css = [
        'skins/all.css'
    ];
    public $depends = [
        JqueryAsset::class
    ];
}