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
/** @var string $type */
/** @var string $key */
/** @var \yihai\core\models\SysReports $model */
/** @var \yihai\core\report\BaseReport $reportClass */
/** @var \yihai\core\modules\system\ModuleSetting $systemSetting */
\yihai\core\assets\ReportAsset::register($this);
$this->title = Yihai::t('yihai','Laporan').' ('.$model->key.')';
if($model->set_header_use_system){
    $header_html = $systemSetting->reportHeader;
    echo $header_html;
}
echo Html::beginTag('div', ['class' => 'main-report']);
echo $reportClass->getTemplateRender();
echo Html::endTag('div');
$watermark_image = $model->watermark_image($systemSetting);
if ($model->useWatermark($systemSetting)) {
    $this->registerCss('    
        body:before{
            content: "";
            display: block;
            position: fixed;
            left: 15%;
            background:url("' . $watermark_image->base64_url . '") no-repeat center center !important;
            width: 70%;
            height: 100%;
            opacity : 0.1;
            z-index: -1;
            filter: grayscale(100%);
            background-size: contain !important;
        }
    ');
}
if ($type === 'print') {
    $this->registerJs('
    window.print();
    window.close();
    ');
}