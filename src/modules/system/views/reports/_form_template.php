<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\assets\ReportAsset;
use yihai\core\extension\tinymce\TinyMce;
use yihai\core\extension\tinymce\TinyMceAsset;
use yihai\core\helpers\ArrayHelper;
use yihai\core\theming\Grid;
use yihai\core\theming\Html;
use yii\helpers\Json;


/** @var \yihai\core\web\View $this */
/** @var \yihai\core\models\SysReports $model */
/** @var \yihai\core\base\ModelOptions $modelOptions */
$model->template = $model->templateFormat;
$modelOptions->formButtonContinueEdit = true;
$reportClass = $model->reportClass;
$reportAssetBundle = ReportAsset::register($this);
$reportAssetBundle->depends[] = TinyMceAsset::class;
$reportAssetBundleCss = array_map(function ($v) use ($reportAssetBundle) {
    return Yihai::$app->assetManager->getAssetUrl($reportAssetBundle, $v);
}, $reportAssetBundle->css);
$htmlGrid = Grid::begin();
$htmlGrid->beginCol(['md-12']);
$dataVars = ($reportClass->dataVars());
$dataList = [];
if (isset($dataVars['lists'])) {
    $dataList = $dataVars['lists'];
}
$pageFormats = $reportClass->pageFormats();
if (isset($pageFormats[$model->set_page_format])) {
    $pageFormat = $pageFormats[$model->set_page_format];
    $pwidth = $pageFormat[0] / 25.4;
    $pheight = $pageFormat[1] / 25.4;
    if ($model->isPageLanscape) {
        $pwidth = $pageFormat[1] / 25.4;
        $pheight = $pageFormat[0] / 25.4;
    }

    $this->registerJs("
    var REPORT_PAGE_WIDTH = {$pwidth};
    var REPORT_PAGE_HEIGHT = {$pheight};
    ", \yihai\core\web\View::POS_HEAD);
}
if($mpdfOptions = $reportClass->mpdf()){
    $margins = [];
    if(isset($mpdfOptions['margin_left'])){
        $margins['left'] = $mpdfOptions['margin_left'];
    }
    if(isset($mpdfOptions['margin_right'])){
        $margins['right'] = $mpdfOptions['margin_right'];
    }
    if(isset($mpdfOptions['margin_top'])){
        $margins['top'] = $mpdfOptions['margin_top'];
    }
    if(isset($mpdfOptions['margin_bottom'])){
        $margins['bottom'] = $mpdfOptions['margin_bottom'];
    }
    $this->registerJs("var REPORT_MARGINS = ".\yii\helpers\Json::encode($margins).";", \yihai\core\web\View::POS_HEAD);
}

$tinyToolbar = [
    "undo redo | styleselect fontselect fontsizeselect font | bold italic strikethrough underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat | emoticons | toggleWidth",
];
$tinyToolbar[] = implode(' | ', array_map(function ($v) {
    return 'toggleClassLists_' . str_replace(" ", '-', $v);
}, array_keys($dataList)));
$availableFields = $reportClass->getAllAvailableFields();
$availableFieldsList = ArrayHelper::remove($availableFields, 'lists', []);
$availableFieldsGlobal = ArrayHelper::remove($availableFields, 'global', []);
$availableFieldsCondition = ArrayHelper::keys_multi($reportClass->conditions());

$tinyToolbar[] = implode(' | ', array_map(function ($v) {
    return 'fieldList_' . str_replace(" ", '-', $v);
}, array_keys($availableFieldsList)));
$tinyToolbar[] = implode(' | ', array_map(function ($v) {
    return 'fieldGlobal_' . str_replace(" ", '-', $v);
}, array_keys($availableFieldsGlobal)));
// process formatters
$formatters = ArrayHelper::keys_multi($reportClass->formatters());
$tinyToolbar[] = 'codeup_formatters | codeup_conditions';
$tinyToolbar[] = implode(' ', array_map(function ($v) {
    return 'formatters' . str_replace(" ", '-', $v);
}, array_keys($formatters)));
$hints_html = Yihai::t('yihai',"Tidak ada petunjuk yang bisa ditampilkan untuk template ini.");
if($hints = $reportClass->hints()){
//    \yihai\core\theming\BoxCard::begin([
//        'tools_order' => ['collapse'],
//        'isCollapsed' => true,
//        'type' => 'info',
//        'title' => Yihai::t('yihai', 'Petunjuk')
//    ]);
    $aa='';
    foreach($hints as $hint){
        $aa .= Html::tag('div', $hint);
    }
    $hints_html = Html::tag('div', $aa,['style'=>'padding:10px']);
    $hints_html = Html::tag('div',Html::ul($hints, ['encode'=>false,'itemOptions'=>['style'=>'white-space: initial;']]),['style'=>'padding:10px']);
//    \yihai\core\theming\BoxCard::end();
}
echo $form->field($model, 'template', ['inline' => false])->widget(TinyMce::class, [
    'preset' => 'custom',
    'clientOptions' => [
        'body_class' => 'main-report',
        'relative_urls' => true,
        'remove_script_host' => false,
        'convert_urls' => false,
        'height' => '500px',
        'valid_elements' => '*[*]',
        'extended_valid_elements' => '*[*]',
        'valid_children' => '+*[*]',
        'extended_valid_children' => '*[*]',
        'content_css' => $reportAssetBundleCss,
        'cleanup' => false,
        'element_format' => 'html',
        'allow_conditional_comments' => true,
        'plugins' => 'codeupReport print preview searchreplace textcolor colorpicker autolink directionality emoticons code visualblocks visualchars contextmenu fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help noneditable',
        'toolbar' => $tinyToolbar,
        'protect' => [
            '/{%(.*)%}/g',
            '/{{(.*)}}/g',
            '/{#(.*)#}/g'
        ],
        'fix_table_elements' => false,
        'removed_menuitems' => 'newdocument',
        'noneditable_regexp' => new \yii\web\JsExpression('[/<field>(.+?)<\/field>/g]'),
        'table_class_list' => [
            ['title' => 'default', 'value' => 'default-table'],
            ['title' => 'no-border', 'value' => 'no-border']
        ],
//        'force_br_newlines' => false,
//        'force_p_newlines' => false,
//        'forced_root_block' => 'p',
        'inline_boundaries_selector' => 'a[href],code,b,i,strong,em,field',
        'fontsize_formats'=> implode(' ',array_map(function($v){
            return $v.'px';
        },range(3, 40) )),
        'setup'=>new \yii\web\JsExpression("function(editor){
            editor.addSidebar('sidebar-hints', {
                tooltip: '".Yihai::t('yihai','Petunjuk template')."',
                icon: 'help',
                text:'ada',
                onrender:  function(api) {
                    api.element().innerHTML += ".Json::encode($hints_html).";
                },
                onshow: function (api) {
                },
                onhide: function (api) {
                    console.log('Hide panel', api.element());
                }
            });
        }
        ")

    ]
]);
$htmlGrid->endCol();

Grid::end();
$dataListJson = json_encode($dataList);
$availableFieldsListJson = json_encode($availableFieldsList);
$availableFieldsGlobalJson = json_encode($availableFieldsGlobal);
$availableFieldsConditionJson = json_encode($availableFieldsCondition);
$formattersJson = json_encode($formatters);
$this->registerJs("
var reportDataListJson = {$dataListJson};
var reportAvailableFieldsListJson = {$availableFieldsListJson};
var reportAvailableFieldsGlobalJson = {$availableFieldsGlobalJson};
var reportAvailableFieldsConditionJson = {$availableFieldsConditionJson};
var reportFormatters = {$formattersJson};
", \yihai\core\web\View::POS_HEAD);
$this->registerJs(/** @lang JavaScript */ "
$('#sysreports-template').parents('form').on('beforeSubmit', function (event) {
    
    var content_replace = $('#sysreports-template').val().replace(/<\!--%datalist(.+?)\%-->/g,'').replace(/<\!--%end_datalist(.+?)\%-->/g,'')
        .replace(/<\!--\%condition:(.+?)\%-->/g,'').replace(/<\!--%end_condition:(.+?)\%-->/g,'');
    var content = $('<div>'+content_replace+'</div>');
    var dataLists = content.find('[report-list]');
    if(dataLists.length){
        dataLists.each(function(i, data){
            var dataList = $(data).attr('report-list');
            $(data).replaceWith('<!--%datalist:'+dataList+'%-->'+data.outerHTML+'<!--%end_datalist:'+dataList+'%-->');
        });
    }
    var conditions = content.find('[condition]');
    if(conditions.length){
        conditions.each(function(i, data){
            var condition = $(data).attr('condition');
            $(data).replaceWith('<!--%condition:'+condition+'%-->'+data.outerHTML+'<!--%end_condition:'+condition+'%-->');
        });
    }
        tinyMCE.activeEditor.setContent(content.html())
    return true;
});
");

$this->registerCss("
");