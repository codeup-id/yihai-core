<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use yii\web\AssetBundle;

class YihaiGridAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/static/yihai';
    public $css = [
        'grid.css'
    ];

}