<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use Yihai;
use yii\web\AssetBundle;

class MomentWithLocalesAsset extends MomentAsset
{
    public $js = [
        'moment-with-locales.min.js'
    ];
    public static function register($view)
    {
        $language = Yihai::$app->language;
        $view->registerJs("moment.locale('{$language}')");
        return parent::register($view);
    }
}