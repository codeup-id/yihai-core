<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\base\ModuleSetting;
use yihai\core\helpers\Url;
use yihai\core\theming\Html;

/** @var \yihai\core\models\SysSettings $model */
$fieldType = ModuleSetting::FIELD_STRING;
$hint = '';
if(!$model->getIsNewRecord()){
    if($setting = Yihai::$app->settings->getModuleSetting($model->module)){
        $fieldType = $setting->fieldType($model->key);
        $hint = $setting->getAttributeHint($model->key);
    }

}

echo $form->field($model, 'key')->textInput(['disabled'=>'disabled']);

if ($fieldType === ModuleSetting::FIELD_HTML) {
        $model->value = strtr($model->value, Url::getKontenReplacing());
    echo $form->field($model, 'value')->widget(\yihai\core\extension\tinymce\TinyMce::class,[
        'clientOptions' => [
            'relative_urls' => false,
            'remove_script_host' => false,
            'height' => '400px',
        ]])->hint($hint);
} elseif ($fieldType === ModuleSetting::FIELD_YESNO) {
    echo $form->field($model, 'value')->dropDownList([
        '1' => Yihai::t('yihai', 'Ya'),
        '0' => Yihai::t('yihai', 'Tidak')
    ])->hint($hint);
} elseif ($fieldType === ModuleSetting::FIELD_IMAGE) {
    $img = '';
    if ($file = \yihai\core\models\SysSettings::findOne(['key' => $model->key, 'module' => $model->module])->valueFile) {
        $img = Html::tag('div', Html::img($file->urlFile('settings'), ['width' => '200px']), ['style'=>'padding:10px']);
    }
    echo $form->field($model, 'value', ['template'=>'{label}'.$img.'{input}{error}{hint}'])->fileInput([
        '1' => Yihai::t('yihai', 'Ya'),
        '0' => Yihai::t('yihai', 'Tidak')
    ])->hint($hint);
} else {
    echo $form->field($model, 'value')->hint($hint);
}
//echo $form->field($model, 'value');