<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\db;


use Yihai;
use yihai\core\models\UserModel;

/**
 * Trait CreatedUpdatedInfoTrait
 * @package yihai\core\db
 * @property UserModel $created_by_user
 * @property UserModel $updated_by_user
 */
trait DataTrait
{

    public function getCreated_by_user()
    {
        return $this->hasOne(UserModel::class, ['id' => 'created_by']);
    }

    public function getUpdated_by_user()
    {
        return $this->hasOne(UserModel::class, ['id' => 'updated_by']);
    }

    public static function gridID($attribute = 'id', $value = 'id')
    {
        return [
            'attribute' => $attribute,
            'value' => $value,
            'headerOptions' => ['style' => 'width:50px;text-align:center'],
            'contentOptions' => ['style' => 'width:50px;text-align:center']
        ];

    }

    public static function gridCreatedBy()
    {
        return [
            'attribute' => 'created_by',
            'headerOptions' => ['class' => 'grid-th-createdBy text-center'],
            'contentOptions' => ['class' => 'grid-td-createdBy text-center']
        ];
    }

    public static function gridCreatedAt()
    {
        return [
            'attribute' => 'created_at',
            'format' => 'datetime'
        ];
    }

    public static function gridCreatedAtSimple()
    {
        return [
            'attribute' => 'created_at',
            'format' => 'datetime_simple',
            'headerOptions' => ['class' => 'grid-th-createdAt text-center'],
            'contentOptions' => ['class' => 'grid-td-createdAt text-center']
        ];
    }

    public static function gridUpdatedBy()
    {
        return [
            'attribute' => 'updated_by',
            'headerOptions' => ['class' => 'grid-th-updatedBy text-center'],
            'contentOptions' => ['class' => 'grid-td-updatedBy text-center']
        ];
    }

    public static function gridUpdatedAt()
    {
        return [
            'attribute' => 'updated_at',
            'format' => 'datetime'
        ];
    }

    public static function gridUpdatedAtSimple()
    {
        return [
            'attribute' => 'updated_at',
            'format' => 'datetime_simple',
            'headerOptions' => ['class' => 'grid-th-updatedAt text-center'],
            'contentOptions' => ['class' => 'grid-td-updatedAt text-center'],
        ];
    }


    public static function viewUserRole()
    {
        return function ($model) {
                $am = Yihai::$app->getAuthManager();
                if($model->hasProperty('sys_user'))
                    $id = $model->sys_user->id;
                else
                    $id = $model->id;
                $roles = $am->getRolesByUser($id);
                $attributes_roles = [];
                $no=1;
                foreach($roles as $role){
                    $attributes_roles[] = [
                        'label' => $no,
                        'format' => 'raw',
                        'value' => function($model) use($role){
                            return $role->name;
                        }
                    ];
                    $no++;
                }
                return [
                    'Roles' => [
                        'model' => $model,
                        'attributes' => $attributes_roles
                    ],
                ];
            };
    }
}