<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\filters;


class AccessControl extends \yii\filters\AccessControl
{

    public $ruleConfig = ['class' => 'yihai\core\filters\AccessRule'];

}