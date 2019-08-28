<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */


use yihai\core\theming\ActiveForm;
use yihai\core\theming\BoxCard;
use yihai\core\theming\Grid;
use yihai\core\theming\Html;

$_isAjax = Yihai::$app->request->getIsAjax() || Yihai::$app->request->getIsPjax();
/** @var \yihai\core\base\ModelOptions $modelOptions */

$saveBtn = Html::submitButton(Html::icon('file-export') . ' ' . $formButton,
    ['class' => ['btn', 'btn-success']]
);

$form = ActiveForm::begin([]);
$items = [];
$contents = '';
foreach($exportAttributes as $key => $item){
    $items[$key] = $item['label'];
}

$contents .= $form->field($exportForm, 'attributes')->widget(\yihai\core\extension\multiselect\MultiSelect::class, [
    'items' => $items,
]);
$contents .= $form->field($exportForm, 'format')->dropDownList([
    'xlsx'=>'Excel 2007+ (xlsx)',
    'xls'=>'Excel 95+ (xls)',
    'text'=>'TEXT',
    'html'=>'HTML',
    'csv'=>'CSV',
    'pdf'=>'PDF'
]);


if ($_isAjax) {
    $cancelBtn = Html::button(Html::icon('undo') . ' ' . Yihai::t('yihai', 'Batal'),
        ['class' => ['btn', 'btn-default'], 'data-dismiss' => 'modal']
    );
    $htmlGrid = Grid::begin([]);
    $htmlGrid->beginCol(['md-6']);
    echo $contents;
    $htmlGrid->endCol();
    Grid::end();
    echo $saveBtn . ' ' . $cancelBtn;
} else {
    $cancelBtn = Html::a(Html::icon('undo') . ' ' . Yihai::t('yihai', 'Batal'),
        [$modelOptions->actionIndex],
        ['class' => ['btn', 'btn-default']]
    );

    BoxCard::begin([
        'type' => 'primary',
        'footer' => true,
        'tools_order' => ['collapse'],
        'title' => $formTitle,
        'footerContent' => $saveBtn . ' ' . $cancelBtn
    ]);

    $htmlGrid = Grid::begin([]);
    $htmlGrid->beginCol(['md-6']);
    echo $contents;
    $htmlGrid->endCol();
    Grid::end();
    BoxCard::end();
}
ActiveForm::end();
