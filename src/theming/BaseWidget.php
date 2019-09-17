<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;

use Yihai;
use yihai\core\assets\JqueryAsset;
use yii\base\InvalidCallException;
use yii\helpers\Json;
use yii\web\AssetBundle;

class BaseWidget extends \yii\base\Widget
{
    public $clientOptions = [];
    public $options = [];

    public $clientEvents = [];

    public static $stackClass = [];

    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    // fix widget yang ada pada container
    public static function begin($config = [])
    {
        $config['class'] = get_called_class();
        /* @var $widget Widget */
        $widget = Yihai::createObject($config);
        self::$stack[] = $widget;
        self::$stackClass[] = $widget::className();
        return $widget;
    }

    public static function end()
    {
        if (!empty(self::$stack)) {
            $widget = array_pop(self::$stack);
            $widgetClass = array_pop(self::$stackClass);
            if (get_class($widget) === $widgetClass) {
                /* @var $widget Widget */
                if ($widget->beforeRun()) {
                    $result = $widget->run();
                    $result = $widget->afterRun($result);
                    echo $result;
                }

                return $widget;
            }
            throw new InvalidCallException('Expecting end() of ' . get_class($widget) . ', found ' . get_called_class());
        }
        throw new InvalidCallException('Unexpected ' . get_called_class() . '::end() call. A matching begin() is not found.');
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