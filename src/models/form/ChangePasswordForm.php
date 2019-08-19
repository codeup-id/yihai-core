<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\models\form;


use Yihai;
use yihai\core\base\Model;

class ChangePasswordForm extends Model
{
    public $old;
    public $new;
    public $repeat;

    public function rules()
    {
        return [
            [['old', 'new','repeat'], 'required'],
            ['new', 'string', 'min'=>6],
            ['repeat', 'compare', 'compareAttribute' => 'new'],
            ['old', 'checkOld']
        ];
    }

    public function attributeLabels()
    {
        return [
            'old' => Yihai::t('yihai', 'Old password'),
            'new' => Yihai::t('yihai', 'New password'),
            'repeat' => Yihai::t('yihai', 'Repeat new password'),
        ];
    }

    public function checkOld($attribute, $params)
    {
        if(!Yihai::$app->user->identity->validatePassword($this->{$attribute}))
            $this->addError($attribute, Yihai::t('yihai','Incorrect old password.'));
    }

    /**
     * @return bool
     */
    public function updatePassword(){
        return Yihai::$app->user->identity->model->updatePassword($this->new);
    }

}