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
$this->title = Yihai::t('yihai', 'Profil');
$userModel = Yihai::$app->user->identity->model;
$userData = Yihai::$app->user->identity->data;
BoxCard::begin([
    'tools_order' => [],
    'type' => 'success',
    'title' => Html::a(Yihai::t('yihai', 'Ganti kata sandi'), ['/system/change-password'], ['class'=>'btn btn-primary']) . ' '.
        Html::a(Yihai::t('yihai', 'Perbarui info'), ['/system/profile-update'], ['class'=>'btn btn-primary']),
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
        'memberSince',
        'last_time:datetime'
    ]
]);
DetailView::end();
$htmlGrid->endCol();
$htmlGrid->beginCol(['md-6']);
if($userData) {
    $detailViewUserData = DetailView::begin([
        'model' => $userData,
        'attributes' => $userData->infoAttributes() ? $userData->infoAttributes() : []
    ]);
    $detailViewUserData->template = '<tr><th class="text-center" colspan="3">'.Yihai::t('yihai', 'Atribut pengguna').'</th></tr>'.$detailViewUserData->template;
    DetailView::end();
}
$htmlGrid->endCol();
Grid::end();

BoxCard::end();
