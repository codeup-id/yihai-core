<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\elfinder\volume;

use Yihai;

class UserPath extends Local
{
    public function isAvailable()
    {
        if (Yihai::$app->user->isGuest)
            return false;

        return parent::isAvailable();
    }

    public function getUrl()
    {
        $path = strtr($this->path, ['{id}' => Yihai::$app->user->id]);
        return Yihai::getAlias($this->baseUrl . '/' . trim($path, '/'));
    }

    public function getRealPath()
    {
        $path = strtr($this->path, ['{id}' => Yihai::$app->user->id]);
        $path = Yihai::getAlias($this->basePath . '/' . trim($path, '/'));
        if (!is_dir($path))
            mkdir($path, 0777, true);

        return $path;
    }
}