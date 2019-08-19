<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\tinymce;


use yihai\core\extension\elfinder\Assets;
use yii\web\AssetBundle;

class TinyMceElfinderAsset extends AssetBundle
{
    public $sourcePath = __DIR__ .'/assets/fm';
    public $js = [
        'elfinder.js'
    ];
    public $depends = [
        Assets::class
    ];

}