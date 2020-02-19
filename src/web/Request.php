<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web;

/**
 * Class Request
 * @package yihai\core\web
 */
class Request extends \yii\web\Request
{
    public function get_post($key, $default = null)
    {
        $get = $this->get($key, $default);
        return ($get ? $get : $this->post($key, $default));
    }

    public function post_get($key, $default = null)
    {
        $post = $this->post($key, $default);
        return ($post ? $post : $this->get($key, $default));
    }
}