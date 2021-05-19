<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web;


use Yihai;

class View extends \yii\web\View
{
    public function init()
    {
        parent::init();
        if (Yihai::$app->params['noSeo']) {
            $this->registerMetaTag(['name' => 'robots', 'contents' => 'noindex, nofollow']);
            $this->registerMetaTag(['name' => 'googlebot', 'contents' => 'noindex']);
            $this->registerMetaTag(['name' => 'googlebot-news', 'contents' => 'noindex']);
        }
    }
}