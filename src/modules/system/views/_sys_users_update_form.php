<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\theming\Grid;

$htmlGrid = Grid::begin();
$htmlGrid->beginCol(['md-6']);
echo $form->field($model, 'fullname');
$htmlGrid->endCol();
Grid::end();