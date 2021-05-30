<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\elfinder;


use Yihai;
use yihai\core\helpers\ArrayHelper;
use yihai\core\helpers\Url;
use yihai\core\theming\InputWithAddon;
use yii\helpers\Json;
use yii\web\JsExpression;

class SelectImageWidget extends InputWithAddon
{
    public $elfinderOptions = [];
    public $elfinderUrl;
    public $elfinderPathID;
    public $elfinderGetFileCallback;
    public $themes = [];
    public function init()
    {
        parent::init();
        if(!$this->elfinderUrl)
            $this->elfinderUrl = "/system/file-manager/connect?pathId={$this->elfinderPathID}";
        $this->elfinderGetFileCallback = new JsExpression("function(file) {
            {$this->elfinderGetFileCallback};
            jQuery('button.ui-dialog-titlebar-close[type=\"button\"]').click();
        }");
        if($this->themes === true)
            $this->themes = Assets::getThemesListManifest();

        $this->elfinderOptions = ArrayHelper::merge([
            'url'=>$this->elfinderUrl,
            'cssAutoLoad' => [],
            'resizable' => true,
            'customData'=> [
                Yihai::$app->request->csrfParam => Yihai::$app->request->csrfToken,
            ],
            'zIndex'=>88888,
            'themes'=>$this->themes,
            'lang'=>Yihai::$app->language,
            'i18nBaseUrl'=> Assets::getI18nPathUrl(),
            'commandsOptions' => [
                'getfile' => [
                    'oncomplete' => 'destroy'
                ]
            ],
            'getFileCallback' => $this->elfinderGetFileCallback,
        ],$this->elfinderOptions);
    }
    public function run()
    {
        $this->clientEvents['click'] = new JsExpression(/** @lang JavaScript */ "function(){
            $('<div \>').dialog({modal: true, width: \"70%\", title: \"Pilih gambar\", 
                create: function(event, ui) {
                    $(this).elfinder(".Json::encode($this->elfinderOptions).").elfinder('instance');
                }
            });
            return false;
        }");
        parent::run();
        $this->view->registerCss(".ui-front{z-index:99999}.elfinder-dialog{top:0 !important}");
        $this->registerAsset(Assets::class);
        $this->registerClientEvents();
    }
}