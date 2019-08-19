<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web;


use Yihai;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Notification extends Component
{
    /**
     * @var NotificationFormat[][]
     */
    private $_notifications = [];

    private $_groups;

    /**
     * @param array|NotificationFormat $notification
     */
    public function add($notification)
    {
        if (is_array($notification)) {
            $notification['class'] = '\yihai\core\web\NotificationFormat';
            try {
                $notification = Yihai::createObject($notification);
            } catch (InvalidConfigException $e) {
            }
        }
        if (!$notification instanceof NotificationFormat)
            return;

        $this->_notifications[$notification->type][$notification->id] = $notification;
        $this->_groups[$notification->group][$notification->id] = $notification;
    }

    /**
     * @param null|string $type
     * @return null|NotificationFormat[]|NotificationFormat[][]
     */
    public function all($type = null)
    {
        if($type === null)
            return $this->_notifications;
        if(isset($this->_notifications[$type]))
            return $this->_notifications[$type];
        return null;
    }

    public function count()
    {
        return count($this->_notifications);
    }

}