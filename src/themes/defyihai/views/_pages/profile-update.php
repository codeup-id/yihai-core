<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */


use yihai\core\theming\ActiveForm;
use yihai\core\theming\BoxCard;
use yihai\core\theming\Html;

/** @var \yihai\core\web\View $this */
$this->title = Yihai::t('yihai', 'Perbarui Info');
if(!isset($formView)){

    BoxCard::begin([
        'tools_order' => [],
        'title' =>
            Html::a(Yihai::t('yihai', 'Kembali'), ['profile'], ['class' => 'btn btn-default'])
    ]);
    echo Html::tag('div', Yihai::t('yihai', 'Pengguna tidak diizinkan mengubah info pengguna.'));
    BoxCard::end();
}else {
    $form = ActiveForm::begin([]);
    BoxCard::begin([
        'tools_order' => [],
        'title' => Html::submitButton(Html::icon('save') . ' ' . Yihai::t('yihai', 'Simpan'), ['class' => 'btn btn-success']) . ' ' .
            Html::a(Yihai::t('yihai', 'Batal'), ['profile'], ['class' => 'btn btn-default'])
    ]);
    echo $this->renderFile($formView, ['model' => $model, 'form' => $form]);
    BoxCard::end();
    ActiveForm::end();
}