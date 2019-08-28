<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

/** @var \yihai\core\base\ModelOptions $modelOptions */

use yihai\core\theming\ActiveForm;
use yihai\core\theming\BoxCard;
use yihai\core\theming\Grid;
use yihai\core\theming\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$_isAjax = Yihai::$app->request->getIsAjax() || Yihai::$app->request->getIsPjax();

if ($_isAjax) {
    $cancelBtn = Html::button(Html::icon('undo') . ' ' . Yihai::t('yihai', 'Batal'),
        ['class' => ['btn', 'btn-default'], 'data-dismiss' => 'modal']
    );
}else{
    $cancelBtn = Html::a(Html::icon('undo') . ' ' . Yihai::t('yihai', 'Batal'),
        [$modelOptions->actionIndex],
        ['class' => ['btn', 'btn-default']]
    );
}
if (isset($modelImportForm)) {
    if (!$_isAjax)
        BoxCard::begin([
            'title' => Yihai::t('yihai','Import')
        ]);
    $htmlGrid = Grid::begin();
    $htmlGrid->beginCol(['md-6']);
    $form = ActiveForm::begin();
    echo $form->field($modelImportForm, 'file')->fileInput([]);
    echo Html::submitButton(Yihai::t('yihai', 'Kirim'), ['class' => 'btn btn-success']);
    echo ' '.$cancelBtn;
    ActiveForm::end();
    $htmlGrid->endCol();
    $htmlGrid->beginCol(['md-6']);
    echo Html::tag('div', Yihai::t('yihai', 'Penting!'), ['style'=>'font-weight:bold']);
    echo Html::tag('div', Yihai::t('yihai', 'Pastikan file yang diunggah cocok dengan format templat.'));
    $urlDownload = $modelOptions->getActionUrlTo('import',['downloadtemplate'=>1]);
    if($custom){
        $urlDownload = $modelOptions->getActionUrlTo('import',['downloadtemplate'=>1, 'custom'=>$custom]);
    }
    echo Html::a(Html::icon('download'). ' '. Yihai::t('yihai', 'Unduh templat.'), $urlDownload, ['class'=>'btn btn-primary']);
    $htmlGrid->endCol();
    Grid::end();
    if (!$_isAjax)
        BoxCard::end();
} elseif (isset($modelImport)) {
    $form = ActiveForm::begin();
    BoxCard::begin([
        'title' => Html::submitButton(Yihai::t('yihai', 'Import'), ['class'=>'btn btn-success']). ' '. Html::a(Yihai::t('yihai', 'Batal'), '?cancel', ['class' => 'btn btn-danger'])
    ]);

    echo '<table class="table table-bordered">';

    echo '<tr><th>Import</th>';
    foreach ($modelOptions->getImportAttributes() as $attribute) {
        echo '<th>' . $model->getAttributeLabel($attribute['label']) . '</th>';
    }
    echo '<th></th></tr>';

    foreach ($modelImport as $i => $m) {
        $valid = $m->validate();
        echo '<tr>';
        echo '<td>';
        echo $valid ? $form->field($importCheck, "import[{$i}]")->checkbox(['checked' => 1]) : '';
        foreach ($modelOptions->getImportAttributes() as $attribute) {
            echo '<td>';
            $dataKey = $attribute['data'];
            $keys = explode('.',$dataKey);
            if(count($keys) > 1){
                $top = $keys[0];
                unset($keys[0]);
                echo ArrayHelper::getValue($m->{$top}, implode('.', $keys));
            }else{
                echo $m->{$keys[0]};
            }
            echo '</td>';
        }
        echo '<td>';
        if ($valid) {
            echo '<div class="text-success">Valid</div>';
        } else {
            foreach ($m->getErrors() as $attribute => $err) {
                echo '<div class="text-danger">';
                echo implode("<br/>", $err) . '<br/>';
                echo '</div>';
            }
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    BoxCard::end();
    ActiveForm::end();
}