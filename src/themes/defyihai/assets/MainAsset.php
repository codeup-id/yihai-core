<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\themes\defyihai\assets;


use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{

    public $publishOptions = [
        'forceCopy' => true
    ];
    public $sourcePath = __DIR__ .'/static';

    public $css = [
        'css/AdminLTE.min.css',
        'css/skins/_all-skins.min.css',
        'css/styles.css'
    ];

    public $js = [
        'js/scripts.js'
    ];

    public $depends = [
        '\yihai\core\assets\FontAwesomeAsset',
        '\yihai\core\assets\JqueryAsset',
        '\yihai\core\assets\BootstrapAsset',
        '\yihai\core\assets\JquerySlimScrollAsset',
        'yii\web\YiiAsset',
    ];

}