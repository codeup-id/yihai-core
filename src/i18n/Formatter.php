<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\i18n;


use Yihai;
use yihai\core\models\UserModel;

class Formatter extends \yii\i18n\Formatter
{
    public function asUsername($userId)
    {
        if($user = UserModel::findOne($userId)){
            return $user->username;
        }
        return null;

    }
    public function asUserinfo($userId)
    {
        if($user = UserModel::findOne($userId)){
            return $user->id.'|'.$user->username.'|'.$user->group;
        }
        return null;

    }

    public function asYesno($value)
    {
        if($value)
            return Yihai::t('yihai','Yes');
        return Yihai::t('yihai','No');
    }

    public function asStrip_tags($value)
    {
        return strip_tags($value);
    }

    public function asDatetime_simple($value)
    {
        return parent::asDatetime($value, 'php:Y-m-d H:i:s');
    }
}