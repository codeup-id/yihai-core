<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


use yihai\core\models\UserModel;
use yii\base\BaseObject;
use yii\web\IdentityInterface;

/**
 * Class UserIdent
 * @package yihai\core\base
 * @property UserModel $model
 * @property IdentGroupInterface $data
 */
class UserIdent extends BaseObject implements IdentityInterface
{
    public $id;

    public $username;
    public $email;
    /** @var UserModel */
    private $_model;

    /**
     * data user
     * @var IdentGroupInterface
     */
    private $_data;

    /**
     * group user
     * @var string
     */
    public $group = '';

    /**
     * default group.
     * @var string
     */
    public static $defGroup = 'system';

    /**
     * @param UserModel $model
     */
    public function setModel($model)
    {
        $this->username = $model->username;
        $this->email = $model->email;
        $this->_model = $model;
        $this->group = $this->model->group;
    }

    /**
     * @return UserModel
     */
    public function getModel()
    {
        return $this->_model;
    }

    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * @return IdentGroupInterface
     */
    public function getData()
    {
        return $this->_data;
    }
    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        $user = UserModel::findOne(['id' => $id, 'status' => UserModel::STATUS_ACTIVE]);
        if (!$user) return NULL;
        return new static(['id' => $user->id, 'model' => $user, 'data' => $user->datauser]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @param string|null $group
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null, $group = null)
    {
        $user = UserModel::findOne([
            'access_token' => $token,
            'status' => UserModel::STATUS_ACTIVE,
            'group' => $group ? $group : static::$defGroup
        ]);
        if (!$user) return NULL;
        return new static(['id' => $user->id, 'model' => $user, 'data' => $user->datauser]);
    }

    /**
     * Finds user by ID
     *
     * @param int|string $id
     * @param string|null $group
     * @return static|null
     */
    public static function findByID($id, $group = null)
    {
        $user = UserModel::find()
            ->andWhere([
                'status'=>UserModel::STATUS_ACTIVE,
                'group' => $group ? $group : static::$defGroup
            ])
            ->andWhere(['id'=>$id])
            ->one();
        if (!$user)
            return null;
        return new static(['id' => $user->id, 'model' => $user, 'data' => $user->datauser]);
    }

    /**
     * Finds user by form login
     *
     * @param string $username
     * @param string|null $group
     * @return static|null
     */
    public static function findForLogin($username, $group = null)
    {
        $username = trim($username);
        $user = UserModel::find()
            ->andWhere([
                'status'=>UserModel::STATUS_ACTIVE,
                'group' => $group ? $group : static::$defGroup
            ])
            ->andWhere(['or',
                ['username'=>$username],
                ['email'=>$username]
            ])
            ->one();
        if (!$user)
            return null;
        return new static(['id' => $user->id, 'model' => $user, 'data' => $user->datauser]);
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @param string|null $group
     * @return static|null
     */
    public static function findByUsername($username, $group = null)
    {
        $user = UserModel::find()
            ->andWhere([
                'status'=>UserModel::STATUS_ACTIVE,
                'group' => $group ? $group : static::$defGroup
            ])
            ->andWhere(['username'=>$username])
            ->one();
        if (!$user)
            return null;
        return new static(['id' => $user->id, 'model' => $user, 'data' => $user->datauser]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->model->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->model->auth_key === $authKey;

    }


    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yihai::$app->security->validatePassword($password, $this->model->password);
    }
}