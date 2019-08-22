<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\theming\Html;

/** @var \yihai\core\base\ModuleSetting $model */
$this->title = Yihai::t('yihai', 'Settings Module "{module}"', ['module' => $model->getModuleId()]);
$form = \yihai\core\theming\ActiveForm::begin();
\yihai\core\theming\BoxCard::begin([
    'title' => 'Form',
    'tools_order' => [],
    'type' => 'primary',
    'footer' => true,
    'footerContent' => \yihai\core\theming\Button::widget(['label' => Yihai::t('yihai', 'Save Settings'), 'type' => 'primary'])
]);
$htmlGrid = \yihai\core\theming\Grid::begin();
foreach ($model->attributes() as $attribute) {
    echo $htmlGrid->beginCol(['md-6'],['class'=>'']);
    $fieldType = $model->fieldType($attribute);
    if ($fieldType === $model::FIELD_HTML) {
        echo $form->field($model, $attribute)->widget(\yihai\core\extension\tinymce\TinyMce::class,[
            'clientOptions' => [
                'relative_urls' => false,
                'remove_script_host' => false,
                'height' => '400px',
                'force_br_newlines' => false,
                'force_p_newlines' => false,
                'forced_root_block' => 'div',
            ]]);
    } elseif ($fieldType === $model::FIELD_YESNO) {
        echo $form->field($model, $attribute)->dropDownList([
            '1' => Yihai::t('yihai', 'Yes'),
            '0' => Yihai::t('yihai', 'No')
        ]);
    } elseif ($fieldType === $model::FIELD_IMAGE) {
        $img = '';
        if ($file = \yihai\core\models\SysSettings::findOne(['key' => $attribute, 'module' => $model->getModuleId()])->valueFile) {
            $img = Html::tag('div', Html::img($file->urlFile('settings'), ['width' => '200px']), ['style'=>'padding:10px']);
        }
        echo $form->field($model, $attribute, ['template'=>'{label}'.$img.'{input}{error}{hint}'])->fileInput([
            '1' => Yihai::t('yihai', 'Yes'),
            '0' => Yihai::t('yihai', 'No')
        ]);
    } else {
        echo $form->field($model, $attribute);
    }
    echo $htmlGrid->endCol();
}
\yihai\core\theming\Grid::end();
\yihai\core\theming\BoxCard::end();
\yihai\core\theming\ActiveForm::end();