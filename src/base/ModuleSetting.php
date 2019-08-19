<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


use Yihai;
use yihai\core\behaviors\UploadBehavior;
use yihai\core\helpers\Url;
use yihai\core\models\SysSettings;
use yii\helpers\ArrayHelper;

class ModuleSetting extends Model
{
    const FIELD_STRING = 'string';
    const FIELD_HTML = 'html';
    const FIELD_IMAGE = 'image';
    const FIELD_YESNO = 'yesno';
//    public $_upload;
    private $_moduleId = 'system';
    private $_behaviors =[

    ];
    public function init()
    {
        parent::init();
    }

    public function behaviors()
    {
        return $this->_behaviors;
    }

    /**
     * @return string
     */
    public function getModuleId()
    {
        return $this->_moduleId;
    }

    public function setModuleId($moduleId)
    {
        $this->_moduleId = $moduleId;
    }

    public function fieldTypes()
    {
        return [

        ];
    }

    public function fieldType($attribute)
    {
        if (isset($this->fieldTypes()[$attribute]))
            return $this->fieldTypes()[$attribute];
        return self::FIELD_STRING;
    }


    public function rules()
    {
//        [['','image', 'extensions' => '', 'maxSize' => ]
        $rules =[];
        foreach($this->attributes() as $attribute){
            if($this->fieldType($attribute) === self::FIELD_IMAGE){
                $rules[] = [$attribute, 'file', 'extensions' => 'jpg,png,jpeg,gif', 'maxSize'=> 1024 * 1024 * 1];
                $this->_behaviors[$attribute] = [
                    'class' => UploadBehavior::class,
                    'deleteOldFile' => true,
                    'group' => 'settings',
                    'attribute' => $attribute,
                    'savedAttribute' => $attribute,
                    'uploadPath' => '@yihai/storages/settings',
                    'autoSave' => true,
                    'autoDelete' => true,

                ];
//                $rules[] = [$attribute, 'file', 'extensions' => 'jpg,png,jpeg,gif', 'maxSize'=> 1024 * 1024 * 1];
            }else{
                $rules[] = [$attribute, 'string'];
            }
        }
        return $rules;
    }


    public function setup_default()
    {
        foreach ($this->attributes() as $attribute) {
            if (SysSettings::find()->where(['module' => $this->_moduleId, 'key' => $attribute])->count() == 0) {
                $sys_setting = new SysSettings();
                $sys_setting->module = $this->_moduleId;
                $sys_setting->key = $attribute;
                $default = $this->{$attribute};
                $sys_setting->value = $default;
                $sys_setting->save(false);
                $this->{$attribute} = $default;
            }
        }

    }

    public function loadSettings()
    {
        foreach ($this->attributes() as $attribute) {

            $value = SysSettings::findOne(['key' => $attribute, 'module' => $this->_moduleId])->value;
            if($this->fieldType($attribute) === self::FIELD_HTML)
                $value = strtr($value, Url::getKontenReplacing());
            $this->{$attribute} = $value !== null ? $value : null;
        }
    }

    /**
     * @return static|null
     */
    public function loadAll()
    {
        if(!$setting = Yihai::$app->settings->getModuleSetting($this->_moduleId))
            return null;
        $data = SysSettings::loadAllModule($this->_moduleId)->all();
        foreach($data as $d){
            if($setting->canSetProperty($d->key)){
                $value = $d->value;
                if($this->fieldType($d->key) === self::FIELD_IMAGE)
                    $value = $d->valueFile;
                if($this->fieldType($d->key) === self::FIELD_HTML)
                    $value = strtr($value, Url::getKontenReplacing());
                $setting->{$d->key} = $value;
            }
        }
        return $setting;
    }


}