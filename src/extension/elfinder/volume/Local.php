<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */


namespace yihai\core\extension\elfinder\volume;

use Yihai;

class Local extends Base
{


    public function getUrl()
    {
        $userPath = '';
        if ($this->withUserPath)
            $userPath = '/' . Yihai::$app->user->id;
        return Yihai::getAlias($this->baseUrl . '/' . trim($this->path, '/') . $userPath);
    }

    public function getRealPath()
    {
        $userPath = '';
        if ($this->withUserPath)
            $userPath = '/' . Yihai::$app->user->id;
        $path = Yihai::getAlias($this->basePath . '/' . trim($this->path, '/') . $userPath);

        if (!is_dir($path))
            mkdir($path, 0777, true);

        return $path;
    }

    protected function optionsModifier($options)
    {

        $options['path'] = $this->getRealPath();
        $options['URL'] = $this->getUrl();

        return $options;
    }
} 