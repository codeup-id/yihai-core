<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\behaviors;


use Exception;
use yihai\core\models\SysAutoNumber;
use yii\db\BaseActiveRecord;
use yii\db\StaleObjectException;

/**
 * Class AutoNumberBehavior
 * @package yihai\core\behaviors
 *
 * ```php
 * public function behavior()
 * {
 *     return [
 *         ...
 *         [
 *             'class' => 'yihai\core\behaviors\AutoNumberBehavior',
 *             'value' => date('Ymd').'.?', // "?" akan diganti dengan auto nomor
 *             'digit' => 6, // berapa banyak nomor
 *         ]
 *     ]
 * }
 * ```
 */
class AutoNumberBehavior extends \yii\behaviors\AttributeBehavior
{
    /**
     * @var integer digit number of auto number
     */
    public $digit;
    /**
     * @var mixed Optional.
     */
    public $group;
    /**
     * @var boolean If set `true` number will genarate unique for owner classname.
     * Default `true`.
     */
    public $unique = true;
    /**
     * @var string
     */
    public $attribute;
    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->attribute !== null) {
            $this->attributes[BaseActiveRecord::EVENT_BEFORE_INSERT][] = $this->attribute;
        }
        parent::init();
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function getValue($event)
    {
        if (is_string($this->value) && method_exists($this->owner, $this->value)) {
            $value = call_user_func([$this->owner, $this->value], $event);
        } else {
            $value = is_callable($this->value) ? call_user_func($this->value, $event) : $this->value;
        }
        $group = md5(serialize([
            'class' => $this->unique ? get_class($this->owner) : false,
            'group' => $this->group,
            'attribute' => $this->attribute,
            'value' => $value
        ]));
        do {
            $repeat = false;
            try {
                $model = SysAutoNumber::findOne($group);
                if ($model) {
                    $number = $model->number + 1;
                } else {
                    $model = new SysAutoNumber([
                        'group' => $group
                    ]);
                    $number = 1;
                }
                $model->update_time = time();
                $model->number = $number;
                $model->save(false);
            } catch (Exception $exc) {
                if ($exc instanceof StaleObjectException) {
                    $repeat = true;
                } else {
                    throw $exc;
                }
            }
        } while ($repeat);
        if ($value === null) {
            return $number;
        } else {
            return str_replace('?', $this->digit ? sprintf("%0{$this->digit}d", $number) : $number, $value);
        }
    }
}