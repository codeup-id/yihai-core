<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

/** @var \yihai\core\web\View $this */
/** @var \yihai\core\base\DashboardWidget[] $dashboardWidgets */

$this->title = 'System';
foreach ($dashboardWidgets as $name => $class) {
    try {
        echo $class::widget();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}