<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use yii\web\AssetBundle;

class BsDatetimePickerAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/static/bs-datetimepicker';
    public $css = [
        'css/bootstrap-datetimepicker.min.css'
    ];
    public $js = [
        'js/bootstrap-datetimepicker.min.js'
    ];
    public $depends = [
        'yihai\core\assets\BootstrapAsset'
    ];
}