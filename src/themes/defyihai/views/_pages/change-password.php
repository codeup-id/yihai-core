<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\theming\ActiveForm;
use yihai\core\theming\BoxCard;
use yihai\core\theming\Grid;
use yihai\core\theming\Html;

/** @var \yihai\core\web\View $this */
/** @var \yihai\core\models\form\ChangePasswordForm $modelForm */
$this->title = Yihai::t('yihai', 'Ganti kata sandi');
$form = ActiveForm::begin();
BoxCard::begin([
    'title' => Html::submitButton(Html::icon('save') .' '.Yihai::t('yihai', 'Simpan'), ['class'=>'btn btn-success']) .' ' .
            Html::a(Yihai::t('yihai', 'Batal'), ['profile'], ['class'=>'btn btn-default'])
]);
$htmlGrid = Grid::begin();
$htmlGrid->beginCol(['md-6']);
echo $form->field($modelForm, 'old')->passwordInput();
echo $form->field($modelForm, 'new')->passwordInput();
echo $form->field($modelForm, 'repeat')->passwordInput();
$htmlGrid->endCol();
Grid::end();
BoxCard::end();

ActiveForm::end();