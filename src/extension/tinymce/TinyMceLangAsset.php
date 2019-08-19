<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\tinymce;

use yii\web\AssetBundle;

class TinyMceLangAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/assets/langs';

    public $depends = [
        'yihai\core\extension\tinymce\TinyMceAsset'
    ];
}
