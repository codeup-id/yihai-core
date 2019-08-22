<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\assets;


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
        return $this->baseUrl.'/default_avatar.png';
    }

}