<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use yii\web\AssetBundle;

class ReportAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/static/report';
    public $css = [
        'styles.css'
    ];
    public $js = [
        'plugin-editor.js'
    ];

}