<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web;

use Yihai;
use yihai\core\base\DashboardWidget;
use yihai\core\base\Settings;
use yihai\core\extension\FileManager;
use yihai\core\i18n\Formatter;
use yii\helpers\ArrayHelper;

/**
 * Class WebApplication
 * @package yihai\core\base
 * @property Response $response
 * @property \yihai\core\base\Theme $theme
 * @property User $user
 * @property \yihai\core\extension\SimpleBarcode $simpleBarcode
 * @property FileManager $fileManager
 * @property Formatter $formatter
 * @property Notification $notification
 * @property Settings $settings
 * @property DashboardWidget $dashboardWidget
 * @property \yihai\core\report\ReportComponent $reports
 * @method User getUser()
 * @method Formatter getFormatter()
 */
class Application extends \yii\web\Application
{

    public $copyright = '<strong>Copyright © 2019 <a target="_blank" href="http://codeup.id/">codeup.id</a>.</strong> All rights reserved.';
    public function __construct($config = [])
    {
        Yihai::$app = $this;
        $common_config = require __DIR__ . '/../config/web.php';
        $config = ArrayHelper::merge($common_config, $config);
        Yihai::bootstrap_config($config);
        parent::__construct($config);
    }
}