<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\actions;

use Yihai;
use yii\web\MethodNotAllowedHttpException;

class LogoutAction extends \yii\base\Action
{
    public $method_allowed = ['POST'];
    /**
     * @return bool
     * @throws MethodNotAllowedHttpException
     */
    protected function beforeRun()
    {
        $verb = Yihai::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', $this->method_allowed);
        if (!in_array($verb, $allowed)) {
            // https://tools.ietf.org/html/rfc2616#section-14.7
            Yihai::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $allowed));
            throw new MethodNotAllowedHttpException('Method Not Allowed. This URL can only handle the following request methods: ' . implode(', ', $allowed) . '.');
        }
        return parent::beforeRun();
    }
    public function run(){
        Yihai::$app->user->logout();
        return $this->controller->goHome();
    }
}