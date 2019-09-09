<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\behaviors;


use Yihai;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class BlameableUsernameBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive current user ID value
     * Set this property to false if you do not want to record the creator ID.
     */
    public $createdByAttribute = 'created_by';
    /**
     * @var string the attribute that will receive current user ID value
     * Set this property to false if you do not want to record the updater ID.
     */
    public $updatedByAttribute = 'updated_by';
    /**
     * {@inheritdoc}
     *
     * In case, when the property is `null`, the value of `Yii::$app->user->id` will be used as the value.
     */
    public $value;
    /**
     * @var mixed Default value for cases when the user is guest
     * @since 2.0.14
     */
    public $defaultValue;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdByAttribute, $this->updatedByAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedByAttribute,
            ];
        }
    }

    /**
     * {@inheritdoc}
     *
     * In case, when the [[value]] property is `null`, the value of [[defaultValue]] will be used as the value.
     */
    protected function getValue($event)
    {
        if ($this->value === null && Yihai::$app->has('user')) {
            $userId = Yihai::$app->user->identity->username;
            if ($userId === null) {
                return $this->getDefaultValue($event);
            }

            return $userId;
        } elseif ($this->value === null) {
            return $this->getDefaultValue($event);
        }

        return parent::getValue($event);
    }

    /**
     * Get default value
     * @param \yii\base\Event $event
     * @return array|mixed
     * @since 2.0.14
     */
    protected function getDefaultValue($event)
    {
        if ($this->defaultValue instanceof \Closure || (is_array($this->defaultValue) && is_callable($this->defaultValue))) {
            return call_user_func($this->defaultValue, $event);
        }

        return $this->defaultValue;
    }
}