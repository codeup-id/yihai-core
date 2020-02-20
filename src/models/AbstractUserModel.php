<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\models;

use Yihai;
use yihai\core\base\IdentGroupInterface;
use yihai\core\behaviors\UploadBehavior;

/**
 * Class AbstractUserModel
 * @package yihai\core\base
 *
 * @property UserModel $sys_user
 */
abstract class AbstractUserModel extends \yihai\core\db\ActiveRecord implements IdentGroupInterface
{

    public $user_password;
    public $user_username;
    public $user_email;
    public $user_group;
    public $user_avatar;
    public $user_avatar_upload;

    public function fieldDataId()
    {
        return 'id';
    }

    public function init()
    {
        parent::init();
        $this->user_group = $this->getGroup();
    }

    public function behaviors()
    {
        return [
            [
                'class' => UploadBehavior::class,
                'deleteOldFile' => true,
                'group' => 'user_avatar',
                'attribute' => 'user_avatar_upload',
                'savedAttribute' => 'user_avatar',
                'uploadPath' => '@yihai/storages/avatars',
                'autoSave' => true,
                'autoDelete' => true,

            ],
            'yihai\core\log\LoggableBehavior'
        ];
    }

    public function rules()
    {
        return [
            [['user_username'], 'required'],
            ['user_email', 'email'],
            ['user_email', 'default','value' => function(){
                return $this->user_username.$this->defaultEmailDomain();
            }],
            ['user_password', 'required', 'on' => self::SCENARIO_CREATE],
            ['user_password', 'string', 'min' => 6],
            ['user_password', 'string', 'max' => 64],
            ['user_avatar_upload', 'file', 'skipOnEmpty' => true, 'extensions' => ['jpg', 'jpeg', 'png']],

            [
                ['user_group', 'user_email'],
                'unique',
                'targetClass' => UserModel::class,
                'targetAttribute' => ['user_group' => 'group', 'user_email' => 'email'],
                'when' => function (AbstractUserModel $model) {
                    if (!$model->sys_user) return true;
                    return $model->user_email != $model->sys_user->email;
                }
            ],
            [
                ['user_group', 'user_username'],
                'unique',
                'targetClass' => UserModel::class,
                'targetAttribute' => ['user_group' => 'group', 'user_username' => 'username'],
                'when' => function (AbstractUserModel $model) {
                    if (!$model->sys_user) return true;
                    return $model->user_username != $model->sys_user->username;
                }
            ],
            [['user_username'],'filter','filter'=>'\yii\helpers\Html::encode'],
            ['user_username', 'string','min'=>4, 'max' => 63],
            ['user_username','match',
                'pattern' => '/^(?![_.-])(?!.*[_.-]{2})[a-z0-9._.-]+(?<![_.-])$/',
                'message' => Yihai::t('yihai', "{attribute} tidak valid. tidak ada ({begin}) di awal, tidak ada ({inside}) di dalam, tidak ada ({end}) di akhir, karakter yang diizinkan ({allowed})",[
                    'attribute' => 'Username',
                    'begin' => '_, ., -',
                    'inside' => '__, _, ._, .., --',
                    'end' => '_, ., -',
                    'allowed'=>'a-z,0-9,.,_,-'
                ])
            ],
        ];
    }
    public function defaultEmailDomain(){
        /** @var \yihai\core\modules\system\ModuleSetting $sysSetting */
        $sysSetting = Yihai::$app->settings->getModuleSetting('system');
        return $sysSetting->defaultEmailDomain;
    }

    public function attributeLabels()
    {
        return [
            'fullname' => Yihai::t('yihai', 'Nama lengkap'),
            'user_avatar_upload' => Yihai::t('yihai', 'Avatar pengguna'),
            'user_username' => Yihai::t('yihai', 'Nama pengguna'),
            'user_password' => Yihai::t('yihai', 'Kata sandi'),
            'user_email' => Yihai::t('yihai', 'Email pengguna'),
        ];
    }
    public function attributeHints()
    {
        return [
            'user_username' => Yihai::t('yihai', 'digunakan untuk login.')
        ];
    }

    public function getSys_user()
    {
        return $this->hasOne(UserModel::class, ['data' => 'id'])->where(['group' => $this->getGroup()]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert)
            $user = new UserModel();
        else
            $user = $this->sys_user;
        $user->username = $this->user_username;
        $user->password = $this->user_password;
        $user->email = $this->user_email;
        $user->group = $this->getGroup();
        $user->data = $this->{$this->fieldDataId()};
        $user->avatar = $this->user_avatar;
        if (!$user->save()) {
            if ($insert)
                $this->delete();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this->sys_user->delete() !== false) {
                return true;
            }
        }
        return false;
    }

    public function afterFind()
    {
        if ($this->sys_user) {
            $user = $this->sys_user;
            $this->user_username = $user->username;
            $this->user_email = $user->email;
            $this->user_avatar = $user->avatar;
        }
        parent::afterFind();
    }
    public function fields()
    {
        $fields = parent::fields();
        $fields['fullname'] = function($model){
            return $model->fullname;
        };
        return $fields;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvatarFile()
    {
        return $this->sys_user->getAvatarFile();
    }

    /**
     * @param \yii\db\ActiveQuery|\yii\db\QueryInterface $query
     * @param \yihai\core\base\FilterModel|static $filterModel
     */
    public function onSearch(&$query, $filterModel)
    {

        if (isset($filterModel->{'sys_user.username'}) && $filterModel->{'sys_user.username'} != '') {
            $query->andFilterWhere(['like', 'sys_user.username', $filterModel->{'sys_user.username'}]);
        }
        if (isset($filterModel->{'sys_user.email'}) && $filterModel->{'sys_user.email'} != '') {
            $query->andFilterWhere(['like', 'sys_user.email', $filterModel->{'sys_user.email'}]);
        }
        parent::onSearch($query, $filterModel);
    }
}