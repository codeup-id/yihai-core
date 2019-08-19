<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web;


use yii\base\BaseObject;

class NotificationFormat extends BaseObject
{
    const TYPE_PINNED = 'pinned';
    const TYPE_NOTIFICATION = 'notification';

    public $type = self::TYPE_NOTIFICATION;
    public $id;
    public $group = 'system';
    public $title;
    public $url;
    public $data;
    public $text;
    public $icon;
    public function init()
    {
        parent::init();
        if(!$this->id){
            $this->id = md5($this->text);
        }
    }

}