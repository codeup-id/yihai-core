<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\grid\GridView;
use yihai\core\theming\BoxCard;
use yihai\core\theming\Html;
use yihai\core\theming\Modal;
use yii\helpers\ArrayHelper;
use yihai\core\helpers\Url;
use yii\widgets\Pjax;

/** @var \yihai\core\web\View $this */
/** @var \yihai\core\base\ModelOptions $modelOptions */
/** @var string $_grid_export */
/** @var \yihai\core\modules\system\ModuleSetting $systemSetting */
$this->title = 'Export Grid ' . $modelOptions->baseTitle;
$systemSetting = \yihai\core\modules\system\Module::loadSettings();
if ($_grid_export !== 'csv' && $_grid_export !== 'xlsx') {
    if ($systemSetting->gridExportPrint_header) {
        echo $systemSetting->gridExportPrint_header;
    }
}
echo Html::beginTag('div', ['class' => 'main-data']);
$gridId = 'grid' . str_replace('\\', '-', $modelClass);
/** @var GridView $grid */
$grid = Yihai::createObject(ArrayHelper::merge([
    'class' => GridView::class,
    'id' => $gridId,
    'filterRowOptions' => ['style' => 'display:none'],
    'layout' => Html::tag('div', '{items}', ['class' => 'table-responsive', 'id' => $gridId . '-table']),
    'tableOptions' => ['class' => ['table', 'table-striped', 'table-bordered', 'table-condensed', 'table-hover']],
], $modelOptions->gridViewConfig));
Yihai::$app->formatter->nullDisplay = '-';
$grid->filterPosition = false;
$grid->dataProvider->getSort()->attributes = [];

if ($_grid_export === 'xlsx') {
    foreach ($grid->columns as $column) {
        if ($column instanceof \yii\grid\Column) {
            $styleHeader = isset($column->headerOptions['style']) ? $column->headerOptions['style'] . ';' : '';
            $styleContent = isset($column->contentOptions['style']) ? $column->contentOptions['style'] . ';' : '';
            $styleHeader .= 'text-align:center;border:1px solid;word-wrap:break-word;vertical-align:middle;font-weight:bold';
            $styleContent .= 'border:1px solid;word-wrap:break-word;vertical-align:top;';
            $column->headerOptions['style'] = $styleHeader;
            $column->contentOptions['style'] = $styleContent;
        }
    }
}

$caption = $grid->renderCaption();
$columnGroup = $grid->renderColumnGroup();
$tableHeader = $grid->showHeader ? $grid->renderTableHeader() : false;
$tableBody = $grid->renderTableBody();

$tableFooter = false;

if ($grid->showFooter) {
    $tableFooter = $grid->renderTableFooter();
}

$content = array_filter([
    $caption,
    $columnGroup,
    $tableHeader,
    $tableBody,
    $tableFooter,
]);
echo Html::tag('table', implode("\n", $content), []);
if ($_grid_export === 'print' || $_grid_export === 'pdf' || $_grid_export === 'html' || $_grid_export === 'xlsx') {
    $isXlsx = $_grid_export === 'xlsx';
    echo Html::endTag('div');
    echo Html::beginTag('div', ['class' => 'print-info']);
    echo Html::beginTag('div', ['class' => 'left']);
    echo '<table class="info-table">';
    echo '<tbody>';
    echo '<tr><th>' . Yihai::t('yihai', 'Printed At') . '</th>' . (!$isXlsx ? '<td style="width: 1px">:</td>' : '') . '<td>' . Yihai::$app->formatter->asDatetime(time(), 'php:Y-m-d H:i:s') . '</td></tr>';
    echo '<tr><th>' . Yihai::t('yihai', 'Printed By') . '</th>' . (!$isXlsx ? '<td style="width: 1px">:</td>' : '') . '<td>' . Yihai::$app->user->identity->data->fullname . ' (' .Yihai::$app->user->identity->username .'|'. Yihai::$app->user->identity->model->group . ')</td></tr>';
    echo '<tbody>';
    echo '</table>';
    echo Html::endTag('div');
    echo Html::beginTag('div', ['class' => 'right']);
    $filters = [];
    if ($grid->filterModel) {
        foreach ($grid->filterModel->attributes() as $attr) {
            if ($f = $grid->filterModel->{$attr}) {
                $filters[$attr] = $f;
            }
        }
    }
    if ($filters) {
        echo '<table class="filter-table">';
        echo '<thead><tr><th colspan="3" style="text-align: center">FILTER</th></tr></thead>';
        echo '<tbody>';
        foreach ($filters as $attr => $value) {
            echo '<tr>';
            echo '<th>' . $model->getAttributeLabel($attr) . '</th>';
            if (!$isXlsx)
                echo '<td style="width: 1px;">:</td>';
            echo '<td>' . $value . '</td>';
            echo '</tr>';
        }
        echo '<tbody>';
        echo '</table>';
    }
    echo Html::endTag('div');
    echo Html::endTag('div');
}
echo '<style>
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        body{
          font-size: 11px;
          position: relative;
          width: 100%;
          height: 100%;
          margin:auto auto;

        }
        .main-data table{
            border:1px solid #000000;
            width: 100%;
            border-collapse:collapse;
        }
        .main-data table th{
            border:1px solid #000000;
        }
        .main-data table td{
            border:1px solid #000000;
            vertical-align:top;
        }
        .main-data table th,.main-data table td{
            padding: 3px 5px;
        }
        .print-info{
            width:100%;
        }
        .print-info .left{
            width:60%;
            float:left;
        }
        .print-info .right{
            width:40%;
            float:right;
        }
        .filter-table{
            font-size:12px;
        }
        .left .filter-table th{
            font-weight:normal;
            text-align:left;
        }
        .right .filter-table th{
        text-align:right;
        font-weight:normal;
        }

.text-center{
text-align:center;
}
    </style>';
if ($systemSetting->gridExportPrint_Watermark) {
    echo '<style>
        body:after{
            content: "' . $modelOptions->baseTitle . ' Data";
            position: fixed;
            bottom:0;
            left:0;
            text-align:right;
            width:100%;
            font-size:30px;
            opacity : 0.1;
        }
        body:before{
            content: "";
            display: block;
            position: fixed;
            left: 15%;
            background:url("' . $systemSetting->gridExportWatermark_image->base64_url . '") no-repeat center center !important; 
            width: 70%;
            height: 100%;
            opacity : 0.1;
            z-index: -1;
            filter: grayscale(100%);
            background-size: contain !important;
        }
    </style>';
}

if ($_grid_export === 'html') {
    echo '<script>
//    window.print();
//    window.close();
    </script>';
}