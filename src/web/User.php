<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web;

use yihai\core\base\UserIdent;
use yihai\core\log\ActivityLog;
use yihai\core\models\AbstractUserModel;

/**
 * Class User
 * @package yihai\core\base
 * @property UserIdent $identity
 */
class User extends \yii\web\User
{
    public $groupClass = [];

    public $identityClass = '\yihai\core\base\UserIdent';

    /**
     * @param string $group
     * @return string|null
     */
    public function groupClass($group)
    {
        if(isset($this->groupClass[$group]))
            return $this->groupClass[$group];
        return null;
    }
    /**
     * @return array
     */
    public function groupDropdown()
    {
        $group_name = array_keys($this->groupClass);
        return array_combine($group_name, $group_name);

    }
    /**
     * @param \yihai\core\base\UserIdent $identity
     * @param bool $cookieBased
     * @param int $duration
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        $identity->model->touch('last_time');
        ActivityLog::newLog(ActivityLog::TYPE_LOGIN);
        parent::afterLogin($identity, $cookieBased, $duration);
    }

    protected function beforeLogout($identity)
    {
        ActivityLog::newLog(ActivityLog::TYPE_LOGOUT);
        return parent::beforeLogout($identity);
    }


    public function groupLoginUrl($group)
    {
        $this->loginUrl = ['/system/login/'.$group];
    }
}