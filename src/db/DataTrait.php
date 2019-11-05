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
use yihai\core\modules\system\Module;
use yihai\core\modules\system\ModuleSetting;

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

    public function getCreated_at_simple()
    {
        return Yihai::$app->formatter->asDatetime_simple($this->created_at);
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
            'value' => function ($model) {
                if ($model->created_by) {
                    if (Yihai::$app->params['gridShowUsernameInCreatedUpdated']) {
                        return Yihai::$app->db->cache(function () use ($model) {
                            return UserModel::findOne(['id' => $model->created_by])->username;
                        });
                    }
                    return $model->created_by;
                }
                return '__system';
            },
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
            'value' => function ($model) {
                if ($model->updated_by) {
                    if (Yihai::$app->params['gridShowUsernameInCreatedUpdated']) {
                        return Yihai::$app->db->cache(function () use ($model) {
                            return UserModel::findOne(['id' => $model->updated_by])->username;
                        });
                    }
                    return $model->updated_by;
                }
                return '__system';
            },
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
            if ($model->hasProperty('sys_user'))
                $id = $model->sys_user->id;
            else
                $id = $model->id;
            $roles = $am->getRolesByUser($id);
            $attributes_roles = [];
            $no = 1;
            foreach ($roles as $role) {
                $attributes_roles[] = [
                    'label' => $no,
                    'format' => 'raw',
                    'value' => function ($model) use ($role) {
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