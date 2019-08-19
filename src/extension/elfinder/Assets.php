<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\elfinder;

use Yihai;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class Assets extends AssetBundle
{
	public $sourcePath = '@vendor/studio-42/elfinder';

	public $publishOptions = [
        'except' => [
            'php/',
            'files/',
        ]
	];

	public $css = array(
		'css/elfinder.min.css',
		'css/theme.css',
	);
	public $js = array(
		'js/elfinder.min.js'
	);
	public $depends = array(
		'yii\jui\JuiAsset',
	);

	/**
	 * @param string $lang
	 * @param \yii\web\View $view
	 */
	public static function addLangFile($lang, $view){
		$lang = ElFinder::getSupportedLanguage($lang);

		if ($lang !== false && $lang !== 'en'){
			$view->registerJsFile(self::getPathUrl().'/js/i18n/elfinder.' . $lang . '.js', ['depends' => [Assets::class]]);
		}
	}

    public static function getPathUrl(){
        return Yihai::$app->assetManager->getPublishedUrl("@vendor/studio-42/elfinder");
    }

    public static function getSoundPathUrl(){
        return self::getPathUrl()."/sounds/";
    }
    public static function getI18nPathUrl(){
        return self::getPathUrl()."/js/i18n/";
    }

    /**
     * @param \yihai\core\web\View $view
     * @throws \yii\base\InvalidConfigException
     */
	public static function noConflict($view){
		list(,$path) = Yihai::$app->assetManager->publish(__DIR__."/assets");
		$view->registerJsFile($path.'/no.conflict.js', ['depends' => [JqueryAsset::class]]);
	}
}
