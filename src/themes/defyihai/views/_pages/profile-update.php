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
$this->title = Yihai::t('yihai', 'Update Info');
if(!isset($formView)){

    BoxCard::begin([
        'tools_order' => [],
        'title' =>
            Html::a(Yihai::t('yihai', 'Back'), ['profile'], ['class' => 'btn btn-default'])
    ]);
    echo Html::tag('div', Yihai::t('yihai', 'The user is not allowed to change user info.'));
    BoxCard::end();
}else {
    $form = ActiveForm::begin([]);
    BoxCard::begin([
        'tools_order' => [],
        'title' => Html::submitButton(Html::icon('save') . ' ' . Yihai::t('yihai', 'Save'), ['class' => 'btn btn-success']) . ' ' .
            Html::a(Yihai::t('yihai', 'Cancel'), ['profile'], ['class' => 'btn btn-default'])
    ]);
    echo $this->renderFile($formView, ['model' => $model, 'form' => $form]);
    BoxCard::end();
    ActiveForm::end();
}