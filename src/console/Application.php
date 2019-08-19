<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\console;


use Yihai;
use yihai\core\i18n\Formatter;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Application
 * @package yihai\core\console
 *
 * @property Formatter $formatter
 * @method Formatter getFormatter()
 */
class Application extends \yii\console\Application
{

    public $copyright = '<strong>Copyright © 2019 <a target="_blank" href="http://codeup.id/">codeup.id</a>.</strong> All rights reserved.';
    public function __construct($config = [])
    {
        Yihai::$app = $this;
        $common_config = require __DIR__ . '/../config/console.php';
        $config = ArrayHelper::merge($common_config, $config);
        Yihai::bootstrap_config($config);
        parent::__construct($config);
    }
}