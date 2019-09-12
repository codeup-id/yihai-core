<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\models\form;


use Yihai;
use yihai\core\base\LoginFormInterface;
use yihai\core\base\UserIdent;
use yihai\core\base\Model;

class LoginForm extends Model implements LoginFormInterface
{
    public $username;
    public $password;
    public $group;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password','group'], 'required'],
            ['rememberMe', 'boolean'],
            ['group','in', 'range'=>array_keys(\Yihai::$app->user->groupClass)],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => Yihai::t('yihai', 'Nama pengguna/Email'),
            'password' => Yihai::t('yihai', 'Kata sandi'),
            'rememberMe' => Yihai::t('yihai', 'Ingatkan saya'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yihai::t('yihai','Nama pengguna atau kata sandi salah.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yihai::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return UserIdent|null
     */
    protected function getUser()
    {
        if ($this->_user === false) {
            $this->_user = UserIdent::findForLogin($this->username, $this->group);
        }

        return $this->_user;
    }
}