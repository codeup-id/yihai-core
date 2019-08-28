<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\extension\select2\Select2;
use yihai\core\grid\GridView;
use yihai\core\theming\ActiveForm;
use yihai\core\theming\BoxCard;
use yihai\core\theming\Button;
use yihai\core\theming\Html;
use yihai\core\theming\Modal;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

$this->title = Yihai::t('yihai','Tetapkan Pengguna');
$authManager = Yihai::$app->getAuthManager();
$allRoles = $authManager->getRoles();
$allPermissions = $authManager->getPermissions();
$htmlGrid = \yihai\core\theming\Grid::begin();

echo $htmlGrid->beginCol(['md-6']);
BoxCard::begin([
    'title' => 'Add Role or Permission',
    'tools_order' => []
]);
$form = ActiveForm::begin();
echo $form->field($model, 'role')->widget(Select2::class, [
    'items' => ArrayHelper::map($allRoles, 'name', 'name'),
]);
echo $form->field($model, 'permission')->widget(Select2::class, [
    'options' => [
        'id' => 'assign-select2-role',
    ],
    'items' => ArrayHelper::map($allPermissions, 'name', 'name'),
]);
echo $form->field($model, 'user_id')->widget(Select2::class, [
    //'items' => \yii\helpers\ArrayHelper::map($allRoles, 'name', 'name'),
    'restModel' => [
        'modelClass' => \yihai\core\models\UserModel::class,
        'fields' => 'id,username,group',
        'templateResult' => 'return data.id +" - "+data.username+" - "+data.group;',
        'filter' => ['username'],
        'useModelFilter'=>true
    ],
]);
echo Button::widget([
    'label' => Yihai::t('yihai', 'Tambah Peran'),
    'type' => 'primary',
    'options' => [
        'type' => 'submit'
    ]
]);
ActiveForm::end();
BoxCard::end();
echo $htmlGrid->endCol();

echo $htmlGrid->beginCol(['md-6']);

BoxCard::begin([
    'title' => 'Check user role and permission',
    'tools_order' => []
]);
echo Html::beginForm('', 'get');
echo Select2::widget([
    'name' => 'check-user_id',
    'restModel' => [
        'modelClass' => \yihai\core\models\UserModel::class,
        'fields' => 'id,username,group',
        'templateResult' => 'return data.id +" - "+data.username+" - "+data.group;',
        'filter' => ['username'],
        'useModelFilter'=>true
    ],
]);
echo '<br/>';
echo '<br/>';
echo Button::widget([
    'label' => Yihai::t('yihai', 'Cek '),
    'type' => 'primary',
    'options' => [
        'type' => 'submit'
    ]
]);
echo Html::endForm();
BoxCard::end();
echo $htmlGrid->endCol();
echo $htmlGrid->beginCol(['xs-12']);
if ($check_id = Yihai::$app->request->get('check-user_id')) {
    BoxCard::begin([
        'tools_order' => [],
        'title' => 'Role and permission assign to user "' . $check_id . '"'
    ]);
    $htmlGrid = \yihai\core\theming\Grid::begin();
    echo $htmlGrid->beginCol(['md-6']);
    $roles = $authManager->getRolesByUser($check_id);
    $permissions = $authManager->getPermissionsByUser($check_id);
    $permissions_array = [];
    foreach ($permissions as $p) {
        $permissions_array[$p->name] = $p;
    }
    echo '<h3>Roles</h3>';
    echo GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $roles,
            'pagination' => ['pageSize' => 0]
        ]),
        'columns' => [
            [
                'headerOptions' => ['style' => 'text-align:center'],
                'contentOptions' => ['style' => 'text-align:center'],
                'label' => Html::icon('trash'),
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function ($model) use ($check_id) {
                    return Modal::widget([
                        'toggleButton' => ['label' => Html::icon('trash'), 'encodeLabel' => false],
                        'form' => Html::beginForm('?check-user_id=' . $check_id),
                        'header' => Yihai::t('yihai', 'Hapus peran "{name}"?', ['name' => $model->name]),
                        'body' => Html::hiddenInput('delete-type', Item::TYPE_ROLE) . Html::hiddenInput('delete-role', $model->name),
                        'footer' => Button::widget(['size' => Modal::SIZE_SMALL, 'label' => Yihai::t('yihai', 'Ya'), 'type' => 'danger']) . ' ' .
                            Modal::dismissButton(['label' => Yihai::t('yihai', 'Tidak')]),
                    ]);
                }
            ],
            'name',
            'description',
            [
                'label' => 'Permissions',
                'format' => 'raw',
                'value' => function ($model) use (&$permissions_array, $authManager) {
                    $rolePerm = $authManager->getPermissionsByRole($model->name);
                    $r = [];
                    foreach ($rolePerm as $role) {
                        $r[] = $role->name;
                        unset($permissions_array[$role->name]);
                    }
                    return implode('<br/>', $r);
                }
            ]
        ]
    ]);
    echo $htmlGrid->endCol();
    echo $htmlGrid->beginCol(['md-6']);
    echo '<h3>Permissions</h3>';
    echo GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $permissions_array,
            'pagination' => ['pageSize' => 0]
        ]),
        'columns' => [
            [
                'headerOptions' => ['style' => 'text-align:center'],
                'contentOptions' => ['style' => 'text-align:center'],
                'label' => Html::icon('trash'),
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function ($model) use ($check_id) {
                    return Modal::widget([
                        'toggleButton' => ['label' => Html::icon('trash'), 'encodeLabel' => false],
                        'form' => Html::beginForm('?check-user_id=' . $check_id),
                        'header' => Yihai::t('yihai', 'Hapus peran "{name}"?', ['name' => $model->name]),
                        'body' => Html::hiddenInput('delete-type', Item::TYPE_PERMISSION) . Html::hiddenInput('delete-role', $model->name),
                        'footer' => Button::widget(['size' => Modal::SIZE_SMALL, 'label' => Yihai::t('yihai', 'Ya'), 'type' => 'danger']) . ' ' .
                            Modal::dismissButton(['label' => Yihai::t('yihai', 'Tidak')]),
                    ]);
                }
            ],
            'name',
            'description',
        ]
    ]);
    $htmlGrid->endCol();
    \yihai\core\theming\Grid::end();
    BoxCard::end();

}
echo $htmlGrid->endCol();
$htmlGrid->endCol();
\yihai\core\theming\Grid::end();
