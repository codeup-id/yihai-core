<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\themes\defyihai;


use Yihai;
use yihai\core\base\ThemeInterface;

/**
 *
 * @property string $path
 */
class Theme implements ThemeInterface
{
    public $skin = 'skin-blue';
    /**
     * nama dari thema
     * @return string
     */
    public function getName()
    {
        return 'defyihai';
    }

    /**
     * path dari thema
     * @return string
     */
    public function getPath()
    {
        return __DIR__;
    }

    /**
     * @return array
     */
    public function getContainer()
    {
        return [

        ];
    }

    /**
     * path map yang akan ditambah pada Yii::$app->view->theme
     * @return array
     */
    public function getPathMap()
    {
        return [];
    }

}