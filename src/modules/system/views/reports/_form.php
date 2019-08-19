<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\models\SysReports;
use yihai\core\theming\Html;
use yii\helpers\ArrayHelper;
/** @var \yihai\core\web\View $this */
/** @var \yihai\core\models\SysReports $model */
$htmlGrid = \yihai\core\theming\Grid::begin();
$htmlGrid->beginCol(['lg-3','md-4']);
echo $form->field($model, 'key');
echo $form->field($model, 'desc')->textarea();
$htmlGrid->endCol();
$htmlGrid->beginCol(['lg-3','md-4']);
/** @var \yihai\core\modules\system\ModuleSetting $sysSetting */
$sysSetting = \yihai\core\modules\system\Module::loadSettings();
echo $form->field($model, 'set_use_watermark')->dropDownList([
    0 => Yihai::t('yihai', 'Use system setting').' ('.($sysSetting->reportWatermark?Yihai::t('yihai','Yes'):Yihai::t('yihai','No')).')',
    1 => Yihai::t('yihai', 'Yes'),
    -1 => Yihai::t('yihai', 'No'),
]);
echo $form->field($model, 'set_use_watermark_image_system')->dropDownList([
    1 => Yihai::t('yihai', 'Yes'),
    0 => Yihai::t('yihai', 'No'),
]);
echo Html::beginTag('div', ['id'=>'custom-watermark-upload','style'=>'display:'.($model->set_use_watermark_image_system?'none':'block')]);
echo $form->field($model, 'set_watermark_image_upload')->fileInput();
if($model->watermark_image)
    echo \yihai\core\theming\Html::img($model->watermark_image->urlFile(),['width'=>'100px']);
echo Html::endTag('div');

echo $form->field($model, 'set_header_use_system')->dropDownList([
    1 => Yihai::t('yihai', 'Yes'),
    0 => Yihai::t('yihai', 'No'),
]);
$htmlGrid->endCol();
$htmlGrid->beginCol(['lg-3','md-4']);
$page_format_list = array_keys($model->reportClass->pageFormats());
echo $form->field($model, 'set_page_format')->widget(\yihai\core\extension\select2\Select2::class,[
    'items' => array_combine($page_format_list, $page_format_list)
]);
echo $form->field($model, 'set_page_orientation')->widget(\yihai\core\extension\select2\Select2::class,[
    'items' => [
        'P' => 'PORTRAIT',
        'L' => 'LANSCAPE'
    ]
]);
$htmlGrid->endCol();
$htmlGrid->beginCol(['xs-12']);
$htmlGrid->endCol();
\yihai\core\theming\Grid::end();

$this->registerJs("
$('#sysreports-set_use_watermark_image_system').change(function(){
    var custom_upload = $('#custom-watermark-upload');
    var val = $(this).val();
    if(val == 1){
        custom_upload.hide();
    }else{
        custom_upload.show();
    }
});
");