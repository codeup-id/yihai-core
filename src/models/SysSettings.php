<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\models;


use Yihai;
use yihai\core\base\ModelOptions;
use yihai\core\behaviors\BlameableBehavior;
use yihai\core\behaviors\TimestampBehavior;
use yihai\core\db\ActiveRecord;
use yihai\core\db\DataTrait;
use yihai\core\helpers\Url;
use yihai\core\log\LoggableBehavior;
use yihai\core\web\Application;

/**
 * Class SysSettings
 * @package yihai\core\models
 * @property int $id [int(11)]
 * @property string $key [varchar(50)]
 * @property string $module [varchar(50)]
 * @property string $value [blob]
 * @property string $created_by [varchar(64)]
 * @property int $created_at [int(11)]
 * @property string $updated_by [varchar(64)]
 * @property int $updated_at [int(11)]
 *
 * @property SysUploadedFiles $valueFile
 */
class SysSettings extends ActiveRecord
{
    use DataTrait;

    public static function tableName()
    {
        return '{{%sys_settings}}';
    }

    private $_behaviors = [];

    public function behaviors()
    {
        $behaviors = array_merge($this->_behaviors, [
            TimestampBehavior::class,
        ]);
        if (Yihai::$app instanceof Application) {
            $behaviors[] = LoggableBehavior::class;
            $behaviors[] = BlameableBehavior::class;
        }

        return $behaviors;
    }
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $update = array_flip($scenarios[parent::SCENARIO_DEFAULT]);
        unset($update['key']);
        $scenarios[parent::SCENARIO_UPDATE] = array_flip($update);
        $scenarios[parent::SCENARIO_CREATE] = array_flip($update);
        return $scenarios;
    }

    public function addBehavior($behavior)
    {
        $this->_behaviors[] = $behavior;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
            [['value', 'created_by', 'updated_by'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['key', 'module'], 'string', 'max' => 50],
            [['key', 'module'], 'unique', 'targetAttribute' => ['key','module']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => Yihai::t('yihai', 'Key'),
            'value' => Yihai::t('yihai', 'Value'),
            'created_by' => Yihai::t('yihai', 'Created By'),
            'created_at' => Yihai::t('yihai', 'Created At'),
            'updated_by' => Yihai::t('yihai', 'Updated By'),
            'updated_at' => Yihai::t('yihai', 'Updated At'),
        ];
    }

    public function beforeSave($insert)
    {
        $this->value = strtr($this->value, array_flip(Url::getKontenReplacing()));
        return parent::beforeSave($insert);
    }

    public function getValueFile()
    {
        return $this->hasOne(SysUploadedFiles::class, ['id' => 'value']);
    }

    /**
     * @param $module
     * @return \yii\db\ActiveQuery
     */
    public static function loadAllModule($module)
    {
        return static::find()->where(['module'=>$module]);
    }
    protected function _options()
    {
        return new ModelOptions([
            'baseTitle' => 'Settings',
            'actionDelete' => false,
            'actionCreate'=>false,
            'actionView'=>false,
            'gridColumnData' => [
                'key',
                'module',
                'value',
                'created_by',
                'created_at:datetime',
                'updated_by',
                'updated_at:datetime',

            ]
        ]);
    }
}