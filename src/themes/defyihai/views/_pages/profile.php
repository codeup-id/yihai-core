<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\theming\BoxCard;
use yihai\core\theming\Grid;
use yihai\core\theming\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ListView;
/** @var \yihai\core\web\View $this */
$this->title = Yihai::t('yihai', 'Profile');
$userModel = Yihai::$app->user->identity->model;
$userData = Yihai::$app->user->identity->data;
BoxCard::begin([
    'tools_order' => [],
    'type' => 'success',
    'title' => Html::a(Yihai::t('yihai', 'Change Password'), ['/system/change-password'], ['class'=>'btn btn-primary']) . ' '.
        Html::a(Yihai::t('yihai', 'Update Info'), ['/system/profile-update'], ['class'=>'btn btn-primary']),
]);
$htmlGrid = Grid::begin([]);
$htmlGrid->beginCol(['md-6']);
DetailView::begin([
    'model' => $userModel,
    'attributes' => [
        'id',
        'username',
        'email',
        'datauser.fullname',
        'group',
        'statustext',
        'created_at:datetime',
        'created_by',
        'updated_at:datetime',
        'updated_by:username',
        'memberSince'
    ]
]);
DetailView::end();
$htmlGrid->endCol();
$htmlGrid->beginCol(['md-6']);
if($userData) {
    DetailView::begin([
        'model' => $userData,
        'attributes' => $userData->infoAttributes()
    ]);
    DetailView::end();
}
$htmlGrid->endCol();
Grid::end();

BoxCard::end();
