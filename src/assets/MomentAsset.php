<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use yii\web\AssetBundle;

class MomentAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/static/momentjs';
    public $js = [
        'moment.min.js'
    ];

    public function registerPlugin($plugin = [])
    {
        if ($plugin)
            $this->js = array_merge($this->js, $plugin);
        else {
            $this->js[] = 'moment-precise-range.js';
        }
    }
}