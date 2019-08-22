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

    /** @var \yihai\core\modules\system\ModuleSetting $systemSetting */
    $systemSetting = \yihai\core\modules\system\Module::loadSettings();
    $pageFormats = $reportClass->pageFormats();
    $options = ['class'=>'main-report-build'];
    $mpdfOptions = $reportClass->mpdf();
    if (isset($pageFormats[$model->set_page_format])) {
        $pageFormat = $pageFormats[$model->set_page_format];
        $pwidth = $pageFormat[0] / 25.4;
        $pheight = $pageFormat[1] / 25.4;
        if ($model->isPageLanscape) {
            $pwidth = $pageFormat[1] / 25.4;
            $pheight = $pageFormat[0] / 25.4;
        };
        $options['style']="width:{$pwidth}cm;height:{$pheight}cm;";
        if(isset($mpdfOptions['margin_left'])){
            $options['style'] .= ";padding-left:{$mpdfOptions['margin_left']}mm";
        }
        if(isset($mpdfOptions['margin_right'])){
            $options['style'] .= ";padding-right:{$mpdfOptions['margin_right']}mm";
        }
        if(isset($mpdfOptions['margin_top'])){
            $options['style'] .= ";padding-top:{$mpdfOptions['margin_top']}mm";
        }
        if(isset($mpdfOptions['margin_bottom'])){
            $options['style'] .= ";padding-bottom:{$mpdfOptions['margin_bottom']}mm";
        }

    }
    $templates = explode('<div><div class="page-break-always"></div></div>',$reportClass->getTemplateRender());
    echo Html::beginTag('div', ['class'=>'main-report','style'=>'height:600px;overflow:auto']);

    foreach($templates as $i => $template){
        $header_html = '';
        if($model->set_header_use_system){
            $header_html = $systemSetting->reportHeader;
        }
        echo Html::tag('div', ($i===0?$header_html:'').$template, $options);
        echo '<br/>';
    }
    echo Html::endTag('div');
}