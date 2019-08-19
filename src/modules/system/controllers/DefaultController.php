<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system\controllers;


use Yihai;
use yihai\core\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        Yihai::$app->setHomeUrl('index');
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'only' => ['logout', 'index', 'profile', 'profile-update','change-password'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return \Yihai::$app->response->redirect(['/system/login']);
                },
            ],
        ];
    }
    public function actions()
    {
        return [
            'login' => 'yihai\core\actions\LoginAction',
            'logout' => 'yihai\core\actions\LogoutAction',
            'profile' => 'yihai\core\actions\ProfileAction',
            'profile-update' => 'yihai\core\actions\ProfileUpdateAction',
            'change-password' => 'yihai\core\actions\ChangePasswordAction',
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }


}