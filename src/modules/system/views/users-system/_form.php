<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\rbac\RbacHelper;
use yihai\core\theming\Grid;
use yihai\core\theming\Html;
use yii\helpers\ArrayHelper;

/** @var \yihai\core\theming\ActiveForm $form */
/** @var \yihai\core\models\SysUsersSystem $model */

$htmlGrid = Grid::begin();
$htmlGrid->beginCol(['lg-4', 'sm-6']);
echo $form->field($model, 'fullname');
$htmlGrid->endCol();
$htmlGrid->beginCol(['lg-4', 'sm-6']);
echo $form->field($model, 'user_username');
/** @var \yihai\core\modules\system\ModuleSetting $sysSetting */
$sysSetting = \yihai\core\modules\system\Module::loadSettings();
$sysEmail = $sysSetting->defaultEmailDomain;
echo $form->field($model, 'user_email')->widget(\yihai\core\theming\InputWithAddon::class, [
    'addonOptions' => ['id'=>'generateEmailUser'],

]);
echo $form->field($model, 'user_password')->passwordInput();
$htmlGrid->endCol();
$htmlGrid->beginCol(['lg-4', 'sm-6']);
if (!$model->getIsNewRecord() && $model->avatarFile)
    echo Html::tag('div', Html::img($model->avatarFile->url(), ['style' => 'width:100px']), ['style' => 'text-align:center']);
echo $form->field($model, 'user_avatar_upload')->fileInput();
$htmlGrid->endCol();
Grid::end();
$this->registerJs("
$('#generateEmailUser').click(function(){
$('#sysuserssystem-user_email').val($('#sysuserssystem-user_username').val()+'{$sysEmail}')
})
");