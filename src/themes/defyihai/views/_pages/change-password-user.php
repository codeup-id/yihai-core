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
$htmlGrid = Grid::begin();
$htmlGrid->beginCol(['md-6']);
echo $form->field($model, 'new')->passwordInput();
echo $form->field($model, 'repeat')->passwordInput();
$htmlGrid->endCol();
Grid::end();
