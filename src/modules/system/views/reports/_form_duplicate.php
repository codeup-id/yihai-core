<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\models\SysReports;
use yii\helpers\ArrayHelper;

/** @var \yihai\core\web\View $this */
/** @var \yihai\core\models\SysReports $model */
if(($fromId = Yihai::$app->request->getQueryParam('id')) && ($fromReport = SysReports::findOne(['id'=>$fromId]))) {
    $htmlGrid = \yihai\core\theming\Grid::begin();
    $htmlGrid->beginCol(['md-6']);

    echo $form->field($model, 'key');

    echo $form->field($model, 'desc')->textarea();
    $model->class = $fromReport->class;
    $model->module = $fromReport->module;
    $model->template = $fromReport->reportClass->template;
    $model->set_use_watermark = $fromReport->set_use_watermark;
    $model->set_header_use_system = $fromReport->set_header_use_system;
    $model->set_page_format = $fromReport->set_page_format;
    $model->set_page_orientation = $fromReport->set_page_orientation;
    echo $form->field($model, 'class',['template'=>'{input}'])->hiddenInput();
    echo $form->field($model, 'module',['template'=>'{input}'])->hiddenInput();
    echo $form->field($model, 'template',['template'=>'{input}'])->hiddenInput();
    echo $form->field($model, 'set_use_watermark',['template'=>'{input}'])->hiddenInput();
    echo $form->field($model, 'set_header_use_system',['template'=>'{input}'])->hiddenInput();
    echo $form->field($model, 'set_page_format',['template'=>'{input}'])->hiddenInput();
    echo $form->field($model, 'set_page_orientation',['template'=>'{input}'])->hiddenInput();
    $htmlGrid->endCol();
    \yihai\core\theming\Grid::end();
}else{
    echo Yihai::t('yihai', 'Tidak ada kelas laporan untuk digandakan.');
}