<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\theming\Button;
use yihai\core\theming\Html;
use yii\data\ArrayDataProvider;

/** @var string $role */
$this->title = Yihai::t('yihai', 'Users in role: {role}', ['role'=>$role]);
$authManager = Yihai::$app->getAuthManager();
$model= [];
foreach ($authManager->getUserIdsByRole($role) as $userId){
    $userInfo = \yihai\core\models\UserModel::find()->select(['username'])->where(['id'=>$userId])->one();
    $model[] = [
        'id' => $userId,
        'username' => $userInfo->username
    ];
}
$dataProvider = new ArrayDataProvider([
    'allModels' => $model,
    'sort' => [
//        'attributes' => ['name', 'description', 'ruleName', 'data', 'createdAt', 'updatedAt']
    ],
    'pagination' => [
        'pageSize' => 0
    ]
]);
echo Html::beginForm();
\yihai\core\theming\BoxCard::begin([
    'title' => Yihai::t('yihai', 'Assign User'),
    'tools_order' => [],
    'footer' => true,
    'footerContent' => Button::widget([
        'label' => Yihai::t('yihai', 'Assign User'),
        'type' => 'primary'
    ])
]);
$allPermissions = $authManager->getPermissions();

foreach ($model as $name => $permission) {
    if (isset($allPermissions[$name]))
        unset($allPermissions[$name]);
}
echo \yihai\core\extension\select2\Select2::widget([
    'restModel' => [
        'modelClass' => \yihai\core\models\UserModel::class,
        'fields' => 'id,username,group',
        'appendQuery' => [
        ],
        'templateResult' => 'return data.id+" - "+data.username'
    ],
    'name' => 'assign-user',
    'options' => [
        'multiple' => true
    ]
]);
\yihai\core\theming\BoxCard::end();
echo Html::endForm();
\yihai\core\theming\BoxCard::begin();

echo \yihai\core\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label' => 'Delete',
            'headerOptions' => ['class' => 'text-center', 'style'=>'width:100px'],
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'raw',
            'value' => function ($model) use ($type, $authManager) {
                return Html::beginForm().Html::hiddenInput('delete-assign', $model['id']). Button::widget(['encodeLabel' => false,'size' => Button::SIZE_XS,'label' => Html::icon('trash')]).Html::endForm();
            }
        ],
        [
            'attribute' => 'id',
            'label' => Yihai::t('yihai', 'User ID')
        ],
        'username',
    ]
]);
\yihai\core\theming\BoxCard::end();