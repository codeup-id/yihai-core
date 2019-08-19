<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\select2;


use yii\web\AssetBundle;

class Select2BootstrapAsset extends AssetBundle
{
    public $sourcePath = '@bower/select2-bootstrap-theme/dist';
    public $css = [
        'select2-bootstrap.css'
    ];
    public $depends = [
        'yihai\core\extension\select2\Select2Asset',
        'yihai\core\assets\BootstrapAsset'
    ];
}