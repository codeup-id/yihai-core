<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */
/** @var int $type */

/** @var string|null $role */

use yihai\core\helpers\Url;
use yihai\core\rbac\RbacHelper;
use yihai\core\theming\BoxCard;
use yihai\core\theming\Button;
use yihai\core\theming\Html;
use yii\rbac\Item;

if ($type === Item::TYPE_ROLE)
    $this->title = "Roles";
$authManager = Yihai::$app->getAuthManager();
if ($type === Item::TYPE_PERMISSION) {
    if ($role) {
        $model = $authManager->getChildren($role);
        $this->title = "Permissions - " . $role;
    } else {
        $this->title = "Permissions";
        $model = $authManager->getPermissions();
    }
} else
    $model = $authManager->getRoles();
$dataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $model,
    'sort' => [
        'attributes' => ['name', 'description', 'ruleName', 'data', 'createdAt', 'updatedAt']
    ],
    'pagination' => [
        'pageSize' => 0
    ]
]);
$title = [];
if ($type === Item::TYPE_ROLE) {
    $title = [
        Button::widget([
            'label' => Yihai::t('yihai', 'Tambah peran kustom'),
            'type' => 'primary',
            'tag' => 'a',
            'options' => [
                'href' => Url::to(['add-role'])
            ]
        ])
    ];
}
if ($role && $type === Item::TYPE_PERMISSION) {
    if (RbacHelper::roleIsCustomName($role) || RbacHelper::roleIsUserGroupName($role)) {

        echo Html::beginForm();
        $htmlGrid = \yihai\core\theming\Grid::begin();
        $htmlGrid->beginCol(['md-6']);
        \yihai\core\theming\BoxCard::begin([
            'title' => Yihai::t('yihai', 'Tambah Peran'),
            'tools_order' => [],
            'footer' => true,
            'footerContent' => Button::widget([
                'label' => Yihai::t('yihai', 'Tambah'),
                'type' => 'primary'
            ])
        ]);
        echo \yihai\core\extension\select2\Select2::widget([
            'name' => 'add-role',
            'value' => '',
            'items' => \yii\helpers\ArrayHelper::map($authManager->getRoles(), 'name', 'name'),
            'options' => [
                'multiple' => true
            ]
        ]);
        \yihai\core\theming\BoxCard::end();
        $htmlGrid->endCol();
        $htmlGrid->beginCol(['md-6']);
        \yihai\core\theming\BoxCard::begin([
            'title' => Yihai::t('yihai', 'Tambah Permisi'),
            'tools_order' => [],
            'footer' => true,
            'footerContent' => Button::widget([
                'label' => Yihai::t('yihai', 'Tambah Permisi'),
                'type' => 'primary'
            ])
        ]);
        $allPermissions = $authManager->getPermissions();

        foreach ($model as $name => $permission) {
            if (isset($allPermissions[$name]))
                unset($allPermissions[$name]);
        }
        echo \yihai\core\extension\select2\Select2::widget([
            'name' => 'add-permissions',
            'value' => '',
            'items' => \yii\helpers\ArrayHelper::map($allPermissions, 'name', 'name'),
            'options' => [
                'multiple' => true
            ]
        ]);
        \yihai\core\theming\BoxCard::end();
        $htmlGrid->endCol();
        \yihai\core\theming\Grid::end();
        echo Html::endForm();
    }
}
BoxCard::begin([
    'title' => implode(' ', $title)
]);
\yii\widgets\Pjax::begin([
    'enablePushState' => false
]);
echo \yihai\core\grid\GridView::widget([
    'behaviors' => [
        [
            'class' => \dosamigos\grid\behaviors\LoadingBehavior::class
        ]
    ],
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label' => 'Delete',
            'headerOptions' => ['class' => 'text-center', 'style' => ($type === Item::TYPE_PERMISSION && (RbacHelper::roleIsCustomName($role) || RbacHelper::roleIsUserGroupName($role) )? '' : 'display:none')],
            'contentOptions' => ['class' => 'text-center', 'style' => ($type === Item::TYPE_PERMISSION && (RbacHelper::roleIsCustomName($role) || RbacHelper::roleIsUserGroupName($role) )? '' : 'display:none')],
            'format' => 'raw',
            'value' => function ($model) use ($type, $authManager) {
                return Html::beginForm().Html::hiddenInput('delete-child', $model->name). Button::widget(['encodeLabel' => false,'size' => Button::SIZE_XS,'label' => Html::icon('trash')]).Html::endForm();
            }
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($model) use ($type) {
                if ($type === Item::TYPE_ROLE && RbacHelper::roleIsCustomName($model->name))
                    return Html::a($model->name, ['detail-role', 'role' => $model->name], ['data-pjax' => 0]);
                return $model->name;
            }
        ],
        [
            'label' => 'Permissions',
            'headerOptions' => ['class' => 'text-center', 'style' => ($type !== Item::TYPE_ROLE ? 'display:none' : '')],
            'contentOptions' => ['class' => 'text-center', 'style' => ($type !== Item::TYPE_ROLE ? 'display:none' : '')],
            'format' => 'raw',
            'value' => function ($model) use ($type, $authManager) {
                $count = count($authManager->getChildren($model->name));
                return Html::a($count, ['permissions', 'role' => $model->name], ['data-pjax' => 0]);
            }
        ],
        [
            'label' => 'Permissions',
            'headerOptions' => ['class' => 'text-center', 'style' => ($type !== Item::TYPE_PERMISSION ? 'display:none' : '')],
            'contentOptions' => ['class' => 'text-center', 'style' => ($type !== Item::TYPE_PERMISSION ? 'display:none' : '')],
            'format' => 'raw',
            'value' => function ($model) use ($type, $authManager) {
                $count = count($authManager->getPermissionsByRole($model->name));
                return Html::a($count, ['permissions', 'role' => $model->name], ['data-pjax' => 0]);
            }
        ],
        [
            'label' => 'User',
            'headerOptions' => ['class' => 'text-center', 'style' => ($type !== Item::TYPE_ROLE ? 'display:none' : '')],
            'contentOptions' => ['class' => 'text-center', 'style' => ($type !== Item::TYPE_ROLE ? 'display:none' : '')],
            'format' => 'raw',
            'value' => function ($model) use ($type, $authManager) {
                $count = count($authManager->getUserIdsByRole($model->name));
                return Html::a($count, ['users', 'role' => $model->name], ['data-pjax' => 0]);
            }
        ],
        'description',
        'ruleName',
        'data',
        'createdAt:datetime',
        'updatedAt:datetime',
    ]
]);
\yii\widgets\Pjax::end();

BoxCard::end();
