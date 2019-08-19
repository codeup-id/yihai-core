<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use yii\web\AssetBundle;

class JquerySlimScrollAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/static/jquery-slimscroll';
    public $js = [
        'jquery.slimscroll.min.js'
    ];
}