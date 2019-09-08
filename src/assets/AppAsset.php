<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


use Yihai;
use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $logo = 'logo.png';
    public $sourcePath = __DIR__.'/static/app';

    public function getLogoUrl()
    {
        return $this->baseUrl.'/'.$this->logo;
    }

    public function getDefaultAvatar()
    {
        $url = $this->baseUrl;
        if(!$url)
            $url = Yihai::$app->assetManager->getBundle(static::class)->baseUrl;

        return $url.'/default_avatar.png';
    }

}