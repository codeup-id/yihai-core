<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\behaviors;


use yihai\core\models\SysUploadedFiles;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;

class UploadBehavior extends \yii\base\Behavior
{
    /**
     * @var string the directory to store uploaded files. You may use path alias here.
     * If not set, it will use the "upload" subdirectory under the application runtime path.
     */
    public $uploadPath = '@runtime/upload';

    /** @var string group file */
    public $group = null;
    /**
     * @var string  the attribute that will receive the uploaded file
     */
    public $attribute = 'file';

    /**
     * @var string the attribute that will receive the file id
     */
    public $savedAttribute;

    /**
     * @var integer the level of sub-directories to store uploaded files. Defaults to 1.
     * If the system has huge number of uploaded files (e.g. one million), you may use a bigger value
     * (usually no bigger than 3). Using sub-directories is mainly to ensure the file system
     * is not over burdened with a single directory having too many files.
     */
    public $directoryLevel = 0;

    /**
     * @var boolean when true `saveUploadedFile()` will be called on event 'beforeSave'
     */
    public $autoSave = true;

    /**
     * @var boolean when true then related file will be deleted on event 'beforeDelete'
     */
    public $autoDelete = false;

    /**
     * @var boolean
     */
    public $deleteOldFile = false;

    /**
     * @var \Closure|string
     */
    public $saveCallback;

    /**
     * filename format
     * {hash}: hash dari microtime() dan filename
     * {ext}: extensi file
     * {name}: filename without ext
     * {filename}: filename with ext
     * @var string
     */
    public $filenameFormat = '{hash}.{ext}';

    /**
     * @var UploadedFile
     */
    private $_file;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        $event = [];
        if ($this->autoSave) {
            $event[BaseActiveRecord::EVENT_BEFORE_INSERT] = 'beforeSave';
            $event[BaseActiveRecord::EVENT_BEFORE_UPDATE] = 'beforeSave';
        }
        if ($this->autoDelete && $this->savedAttribute !== null) {
            $event[BaseActiveRecord::EVENT_BEFORE_DELETE] = 'beforeDelete';
        }
        return $event;
    }

    /**
     * Get saved file
     * @return SysUploadedFiles
     */
    public function getSavedFile()
    {
        if($this->savedAttribute && $this->owner->{$this->savedAttribute}){
            return SysUploadedFiles::findOne($this->owner->{$this->savedAttribute});
        }
    }
    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($name === $this->attribute) {
            if ($this->_file === null) {
                $this->_file = UploadedFile::getInstance($this->owner, $this->attribute);
            }
            return $this->_file;
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($name === $this->attribute) {
            if ($value instanceof UploadedFile || $value === null) {
                $this->_file = $value;
            }
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $name === $this->attribute || parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $name === $this->attribute || parent::canGetProperty($name, $checkVars);
    }

    /**
     * Save uploaded file into [[$uploadPath]]
     * @param boolean $deleteOldFile If true and file exists, file will be deleted.
     * @return boolean|null if success return true, fault return false.
     * Return null mean no uploaded file.
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function saveUploadedFile($deleteOldFile = null)
    {
        /* @var $file UploadedFile */
        $file = $this->{$this->attribute};
        if ($file !== null) {
            $callback = $this->saveCallback;
            if ($callback !== null && is_string($callback)) {
                $callback = [$this->owner, $callback];
            }
            $model = SysUploadedFiles::saveAs($file, [
                'group' => $this->group,
                'uploadPath' => $this->uploadPath,
                'directoryLevel' => $this->directoryLevel,
                'saveCallback' => $callback,
                'filenameFormat' => $this->filenameFormat
            ]);
            if ($model) {
                if ($this->savedAttribute !== null) {
                    if ($deleteOldFile === null) {
                        $deleteOldFile = $this->deleteOldFile;
                    }
                    $oldId = $this->owner->{$this->savedAttribute};
                    $this->owner->{$this->savedAttribute} = $model->id;
                    if ($deleteOldFile && ($oldModel = SysUploadedFiles::findOne($oldId)) !== null) {
                        return $oldModel->delete();
                    }
                }
                return true;
            }
            return false;
        }
    }

    /**
     * Event handler for beforeSave
     * @param \yii\base\ModelEvent $event
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeSave($event)
    {
        if ($this->saveUploadedFile() === false) {
            $event->isValid = false;
        }
    }

    /**
     * Event handler for beforeDelete
     * @param \yii\base\ModelEvent $event
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete($event)
    {
        $oldId = $this->owner->{$this->savedAttribute};
        if (($oldModel = SysUploadedFiles::findOne($oldId)) !== null) {
            $event->isValid = $event->isValid && $oldModel->delete();
        }
    }
}