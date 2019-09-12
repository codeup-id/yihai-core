<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */


/**
 * Class Yihai
 */
class Yihai extends Yii
{
    public static $version = '1.0.2';
    /** @var \yihai\core\console\Application|\yihai\core\web\Application */
    public static $app;

    /**
     * @return \yihai\core\console\Application|\yihai\core\web\Application
     */
    public static function getApp()
    {
        return self::$app;
    }

    public static function bootstrap_config(&$config)
    {
        $config['aliases']['@yihai-core'] = __DIR__;
    }

    public static function writeRuntimeTmpFile($file, $content)
    {
        $runtimeTmp = Yihai::getAlias('@runtime/tmp');
        if (!is_dir($runtimeTmp)) {
            mkdir($runtimeTmp);
        }
        file_put_contents($runtimeTmp . '/' . $file, $content);
        return $runtimeTmp . '/' . $file;

    }

    /**
     * jika view null, maka tidak akan di register.
     * @param \yihai\core\web\View $view
     * @return \yihai\core\assets\AppAsset
     */
    public static function registerAppAsset($view = null)
    {
        /** @var \yihai\core\assets\AppAsset $class */
        $class = Yihai::$app->params['AppAssetClass'];
        if($view)
            return $class::register($view);
        return new $class();

    }


}
