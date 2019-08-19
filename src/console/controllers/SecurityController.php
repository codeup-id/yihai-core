<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\console\controllers;

use Yihai;
use yihai\core\console\Controller;
use yii\console\ExitCode;

class SecurityController extends Controller
{
    public function actionPasswordHash($password){
        $this->stdout(Yihai::$app->security->generatePasswordHash($password) . "\n");
        return ExitCode::OK;
    }
}