<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\grid\GridView;
use yihai\core\theming\BoxCard;
use yihai\core\theming\Button;
use yihai\core\theming\Html;
use yihai\core\theming\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Data';
$isAjax = Yihai::$app->request->isAjax || Yihai::$app->request->isPjax;
/** @var \yihai\core\web\View $this */
/** @var \yihai\core\base\ModelOptions $modelOptions */
$modal_attr = 'data-toggle="modal" data-target="#yihai-crud-basemodal"';
$links = $links2 = [];
$links_helps = [];
if ($modelOptions->hint) {
    $links_helps[] = \yihai\core\theming\Button::widget([
        'encodeLabel' => false,
        'label' => Html::icon('info'),
        'size' => \yihai\core\theming\Button::SIZE_XS,
        'type' => 'info',
        'clientEvents' => [
            'click' => 'function(){
                $("#data-hint-info").slideToggle();
            }'
        ]
    ]);
}
if ($modelOptions->actionCreate && $modelOptions->userCanAction('create')) {
    $links[] = '<a href="' . Url::to([$modelOptions->getActionUrl('create')]) . '" ' . ($modelOptions->useModalLinkCreate ? $modal_attr : '') . ' class="btn btn-primary">' . Html::icon('file-plus', ['prefix' => 'fal fa-']) . ' ' . Yihai::t('yihai', 'Add') . '</a>';
}
if ($modelOptions->actionImport && $modelOptions->importAttributes && $modelOptions->userCanAction('import')) {
    $links2[] = '<a href="' . Url::to([$modelOptions->getActionUrl('import')]) . '" ' . ($modelOptions->useModalLinkImport ? $modal_attr : '') . ' class="btn btn-primary">' . Html::icon('file-import', ['prefix' => 'fal fa-']) . ' ' . Yihai::t('yihai', 'Import') . '</a>';
}
if ($modelOptions->actionImport && $modelOptions->importCustom && $modelOptions->userCanAction('import')) {
    foreach ($modelOptions->importCustom as $key => $importCustom) {
        $links2[] = '<a href="' . $modelOptions->getActionUrlTo('import', ['custom' => $key]) . '" ' . ($modelOptions->useModalLinkImport ? $modal_attr : '') . ' class="btn btn-primary">' . Html::icon('file-import', ['prefix' => 'fal fa-']) . ' ' . $importCustom['label'] . '</a>';
    }

}
if ($modelOptions->actionExport && $modelOptions->exportAttributes && $modelOptions->userCanAction('export')) {
    $links2[] = '<a href="' . $modelOptions->getActionUrlTo('export') . '" ' . ($modelOptions->useModalLinkExport ? $modal_attr : '') . ' class="btn btn-primary">' . Html::icon('file-export', ['prefix' => 'fal fa-']) . ' ' . Yihai::t('yihai', 'Export') . '</a>';
}
if (!$isAjax) {
    BoxCard::begin([
        'type' => 'primary',
        'title' => Html::tag('div', implode(' ', $links_helps), ['class' => 'btn-group']) . ' ' . Html::tag('div', implode(' ', $links), ['class' => 'btn-group']) . ' ' . Html::tag('div', implode(' ', $links2), ['class' => 'btn-group']),
        'headerBorder' => true,
        'tools_order' => ['collapse'],

    ]);
    if (isset($modelOptions->hint)) {
        $hint = Html::ul($modelOptions->hint);
        echo Html::beginTag('div', ['id' => 'data-hint-info', 'style' => 'display:none']);
        echo \yihai\core\theming\Alert::widget([
            'type' => 'info',
            'title' => Yihai::t('yihai', 'Hint / Info'),
            'icon' => Html::icon('info', ['class' => 'icon']),
            'closeButton' => false,
            'body' => $hint
        ]);
        echo Html::endTag('div');
    }
}
Pjax::begin([
    'id' => 'pjax-' . str_replace('/', '-', $modelOptions->getActionUrl()),
    'enablePushState' => false,
    'timeout' => false,
    'clientOptions' => [
        'method' => 'GET',
    ],
]);
$gridId = 'grid' . str_replace('\\', '-', $modelClass);
echo Html::beginTag('div', ['class' => 'text-right']);

echo Html::beginForm(Yihai::$app->request->url, 'post', ['target' => '_blank', 'style' => 'display:inline-block']);
echo Html::beginTag('div',['class'=>'btn-group']);
if ($modelOptions->gridPrint) {
    echo Button::widget([
        'label' => Html::icon('print'),
        'encodeLabel' => false,
        'size' => Button::SIZE_SM,
        'options' => [
            'title' => Yihai::t('yihai', 'Print'),
            'formaction' => Url::current(['_grid_export' => 'print'])
        ]
    ]);
}
if ($modelOptions->gridPdf) {
    echo Button::widget([
        'label' => Html::icon('file-pdf'),
        'encodeLabel' => false,
        'size' => Button::SIZE_SM,
        'options' => [
            'title' => Yihai::t('yihai', 'Download Pdf'),
            'formaction' => Url::current(['_grid_export' => 'pdf'])
        ]
    ]);
}
if ($modelOptions->gridXlsx) {
    echo Button::widget([
        'label' => Html::icon('file-excel'),
        'encodeLabel' => false,
        'size' => Button::SIZE_SM,
        'options' => [
            'title' => Yihai::t('yihai', 'Download Excel'),
            'formaction' => Url::current(['_grid_export' => 'xlsx'])
        ]
    ]);
}

if ($modelOptions->gridCsv) {
    echo Button::widget([
        'label' => Html::icon('file-csv'),
        'encodeLabel' => false,
        'size' => Button::SIZE_SM,
        'options' => [
            'title' => Yihai::t('yihai', 'Download Csv'),
            'formaction' => Url::current(['_grid_export' => 'csv'])
        ]
    ]);
}

if ($modelOptions->gridHtml) {
    echo Button::widget([
        'label' => Html::icon('table', ['prefix' => 'far fa-']),
        'encodeLabel' => false,
        'size' => Button::SIZE_SM,
        'options' => [
            'title' => Yihai::t('yihai', 'Html'),
            'formaction' => Url::current(['_grid_export' => 'html'])
        ]
    ]);
}
echo Html::endTag('div');
echo Html::endForm();
echo Html::endTag('div');
echo GridView::widget(ArrayHelper::merge([
    'class' => GridView::class,
    'id' => $gridId,
    'layout' => Html::tag('div', '{items}', ['class' => 'table-responsive', 'id' => $gridId . '-table']) . '{summary}' .
        Html::tag('div', '{pager}', ['style' => 'text-align:center']),
    'tableOptions' => ['class' => ['table', 'table-striped', 'table-bordered', 'table-condensed', 'table-hover']],
], $modelOptions->gridViewConfig));
if ($modelOptions->gridViewCheckboxColumn) {
    $this->registerJs('
var grid = $("#' . $gridId . '");
grid.find(".grid-col-select,.select-on-check-all").change(function(){
    var checkList = grid.yiiGridView(\'getSelectedRows\');
    if(checkList.length){
        grid.find(".btn-delete-selected").show();
        grid.find(".btn-delete-selected").attr("data-multiple", JSON.stringify(checkList));
    }else{
        grid.find(".btn-delete-selected").removeAttr("data-multiple");
        grid.find(".btn-delete-selected").hide();
    }
})
');
}
Pjax::end();
if (!$isAjax) {
    BoxCard::end();
}