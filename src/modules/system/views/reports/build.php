<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\theming\Html;

/** @var \yihai\core\web\View $this */
/** @var string $key */
/** @var \yihai\core\models\SysReports $model */
\yihai\core\assets\ReportAsset::register($this);
$reportClass = $model->reportClass;
$this->title = $model->key;
$this->params['titleDesc'] = $reportClass->desc;
$reportClass->build();

\yihai\core\theming\BoxCard::begin([
    'title' => 'Filter',
    'tools_order' => []
]);
$reportClass->renderFilterHtml();

\yihai\core\theming\BoxCard::end();
if($reportClass->isHasBuild()) {
    \yihai\core\theming\BoxCard::begin([
        'options' => ['class' => 'main-report'],
        'tools_order' => [],
        'footer' => true,
    ]);

    echo $reportClass->getTemplateRender();
    \yihai\core\theming\BoxCard::end();
}