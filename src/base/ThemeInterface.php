<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


interface ThemeInterface
{
    /**
     * nama dari thema
     * @return string
     */
    public function getName();
    /**
     * path dari thema
     * ```php
     *  return __DIR__;
     * ```
     * @return string
     */
    public function getPath();

    /**
     * @return array
     */
    public function getContainer();

    /**
     * path map yang akan ditambah pada Yii::$app->view->theme
     * @return array
     */
    public function getPathMap();

    /**
     * Main Asset Class
     * @return string|\yii\web\AssetBundle
     */
    public function mainAssets();
}