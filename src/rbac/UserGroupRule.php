<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\rbac;
use Yihai;
use yii\rbac\Rule;

/**
 * Checks if user group matches
 */
class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    public function execute($user, $item, $params)
    {
        if (!Yihai::$app->user->isGuest) {
            $group = Yihai::$app->user->identity->data->group;
            return $item->name === 'user-group-'.$group;
        }
        return false;
    }
}