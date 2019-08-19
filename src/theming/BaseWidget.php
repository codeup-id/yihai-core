<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


use yihai\core\assets\JqueryAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Json;
use yii\web\AssetBundle;

class BaseWidget extends \yii\base\Widget
{
    public $clientOptions = [];
    public $options = [];

    public $clientEvents = [];

    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * @param AssetBundle|string $assetClass
     * @return AssetBundle
     */
    protected function registerAsset($assetClass)
    {
        $view = $this->getView();
        return $assetClass::register($view);
    }

    protected function registerJquery($pluginName)
    {
        $view = $this->getView();
        JqueryAsset::register($view);

        $id = $this->options['id'];

        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "jQuery('#$id').$pluginName($options);";
            $view->registerJs($js);
        }

        $this->registerClientEvents();
    }

    /**
     * Registers JS event handlers that are listed in [[clientEvents]].
     * @since 2.0.2
     */
    protected function registerClientEvents()
    {
        if (!empty($this->clientEvents)) {
            $id = $this->options['id'];
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('#$id').on('$event', $handler);";
            }
            $this->getView()->registerJs(implode("\n", $js));
        }
    }

}