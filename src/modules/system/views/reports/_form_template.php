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


/** @var \yihai\core\web\View $this */
/** @var \yihai\core\models\SysReports $model */
/** @var \yihai\core\base\ModelOptions $modelOptions */
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
    $dataList = array_keys($dataVars['lists']);
}
$toolbarToggleDataList = '';
foreach ($dataList as $item) {
    $toolbarToggleDataList .= 'toggleClassLists_' . $item . ' ';
}
$availableFields = $reportClass->getAllAvailableFields();
$availableFieldsList = $reportClass->buildAvailableFields(ArrayHelper::remove($availableFields, 'lists', []));
//print_r($availableFieldsList);exit;
$availableFieldsGlobal = $reportClass->buildAvailableFields(ArrayHelper::remove($availableFields, 'global', []));
$tinyToolbar = [
    "undo redo | styleselect font | bold italic strikethrough underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat",
    $toolbarToggleDataList,
];
$tinyToolbar[] = implode(' ', array_map(function ($v) {
    return 'fieldList_' . str_replace(" ", '-', $v);
}, array_keys($availableFieldsList)));
foreach ($availableFieldsList as $name => $fields) {
    $tinyToolbar[] = implode(' ', array_map(function ($v) use ($name) {
        return 'btnField_' . $name . '_' . str_replace(" ", '-', $v);
    }, $fields));
}
$tinyToolbar[] = implode(' ', array_map(function ($v) {
    return 'fieldGlobal_' . str_replace(" ", '-', $v);
}, array_keys($availableFieldsGlobal)));
// process formatters
$formatters = $reportClass->formatters();

$tinyToolbar[] = implode(' ', array_map(function ($v) {
    return 'formatters'  . str_replace(" ", '-', $v);
}, array_keys($formatters)));
echo $form->field($model, 'template', ['inline' => false])->widget(TinyMce::class, [
    'preset' => 'custom',
    'clientOptions' => [
        'body_class' => 'main-report',
        'relative_urls' => false,
        'remove_script_host' => false,
        'height' => '700px',
        'valid_elements' => '*[*]',
        'extended_valid_elements' => '*[*]',
        'valid_children' => '+*[*]',
        'extended_valid_children' => '+*[*]',
        'content_css' => $reportAssetBundleCss,
        'cleanup' => false,
        'element_format' => 'html',
        'allow_conditional_comments' => true,
        'plugins' => 'codeupReport print preview searchreplace autolink directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help noneditable',
        'toolbar' => $tinyToolbar,
        'protect' => [
            '/{%(.*)%}/g',
            '/{{(.*)}}/g',
            '/{#(.*)#}/g'
        ],
        'fix_table_elements' => false,
        'removed_menuitems' => 'newdocument',
        'table_class_list' => [
            ['title' => 'default', 'value' => 'default-table'],
            ['title' => 'no-border', 'value' => 'no-border']
        ]
    ]
]);
$htmlGrid->endCol();

Grid::end();
$dataListJson = json_encode($dataList);
$availableFieldsListJson = json_encode($availableFieldsList);
$availableFieldsGlobalJson = json_encode($availableFieldsGlobal);
$formattersJson = json_encode($formatters);
$this->registerJs("
var reportDataListJson = {$dataListJson};
var reportAvailableFieldsListJson = {$availableFieldsListJson};
var reportAvailableFieldsGlobalJson = {$availableFieldsGlobalJson};
var reportFormatters = {$formattersJson};
", \yihai\core\web\View::POS_HEAD);
$this->registerJs(/** @lang JavaScript */ "
$('#sysreports-template').parents('form').on('beforeSubmit', function (event) {
    
    var content_replace = $('#sysreports-template').val().replace(/<\!--%datalist(.*)\%-->/g,'').replace(/<\!--%end_datalist(.*)%-->/g,'')
        .replace(/<\!--%formatter(.*)\%-->/g,'').replace(/<\!--%end_formatter(.*)%-->/g,'');
    var content = $('<div>'+content_replace+'</div>');
    var dataLists = content.find('[report-list]');
    if(dataLists.length){
        dataLists.each(function(i, data){
            var dataList = $(data).attr('report-list');
            $(data).replaceWith('<!--%datalist:'+dataList+'%-->'+data.outerHTML+'<!--%end_datalist:'+dataList+'%-->');
        });
        tinyMCE.activeEditor.setContent(content.html())
    }
    var formatters = content.find('[formatter]');
    if(formatters.length){
        formatters.each(function(i, data){
            var formatter = $(data).attr('formatter');
            console.log(data.innerHTML);
            $(data).html('<!--%formatter:'+formatter+'%-->'+data.innerHTML+'<!--%end_formatter:'+formatter+'%-->');
        });
        tinyMCE.activeEditor.setContent(content.html())
    }
    return true;
});
");

$this->registerCss("
");