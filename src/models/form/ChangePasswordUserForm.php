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
use yihai\core\models\UserModel;

class ChangePasswordUserForm extends UserModel
{
    public $new;
    public $repeat;

    public function rules()
    {
        return [
            [['new','repeat'], 'required'],
            ['new', 'string', 'min'=>6],
            ['repeat', 'compare', 'compareAttribute' => 'new'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'new' => Yihai::t('yihai', 'New password'),
            'repeat' => Yihai::t('yihai', 'Repeat new password'),
        ];
    }
    public function save($runValidation = true, $attributeNames = null)
    {
        $this->password = $this->new;
        return parent::save($runValidation, $attributeNames);
    }

}