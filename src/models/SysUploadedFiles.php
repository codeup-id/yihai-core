<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\models;


use Yihai;
use yihai\core\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "sys_uploaded_file".
 *
 * @property integer $id
 * @property string $name
 * @property string $group
 * @property string $path
 * @property string $filename
 * @property integer $size
 * @property string $type
 * @property string $ext
 * @property int $created_at [int(11)]
 * @property string $created_by [varchar(64)]
 * @property int $updated_at [int(11)]
 * @property string $updated_by [varchar(64)]
 *
 * @property string $fullpath
 * @property string $content
 * @property string $base64
 * @property string $base64_url
 * @property string $hash [varchar(255)]
 */
class SysUploadedFiles extends ActiveRecord
{
    /**
     * @var string
     */
    public static $defaultUploadPath = '@yihai/storages/upload';
    /**
     * @var integer
     */
    public static $defaultDirectoryLevel = 1;
    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @var string Upload path
     */
    public $uploadPath;
    /**
     * @var integer the level of sub-directories to store uploaded files. Defaults to 1.
     * If the system has huge number of uploaded files (e.g. one million), you may use a bigger value
     * (usually no bigger than 3). Using sub-directories is mainly to ensure the file system
     * is not over burdened with a single directory having too many files.
     */
    public $directoryLevel = 0;
    /**
     * @var \Closure
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
     * @inheritdoc
     */
    private $_microtime;
    public function init()
    {
        parent::init();
        $this->_microtime = microtime();
    }

    public static function tableName()
    {
        return '{{%sys_uploaded_files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false],
            [['uploadPath'], 'default', 'value' => static::$defaultUploadPath],
            [['name', 'size'], 'default', 'value' => function ($obj, $attribute) {
                return $obj->file->$attribute;
            }],
            [['type'], 'default', 'value' => function () {
                return FileHelper::getMimeType($this->file->tempName);
            }],
            [['path'], 'default', 'value' => function () {
                $level = $this->directoryLevel === null ? static::$defaultDirectoryLevel : $this->directoryLevel;
                $base = $this->uploadPath;
                $hash = md5($this->_microtime . $this->file->name);
                if ($level > 0) {
                    for ($i = 0; $i < $level; ++$i) {
                        if (($prefix = substr($hash, 0, 2)) !== false) {
                            $base .= DIRECTORY_SEPARATOR . $prefix;
                            $hash = substr($hash, 2);
                        }
                    }
                }
                return $base . DIRECTORY_SEPARATOR;
            }],
            [['hash'], 'default', 'value' => function () {
                return md5($this->_microtime . $this->file->name);
            }],
            [['ext'], 'default', 'value' => function () {
                return $this->file->getExtension();
            }],
            [['filename'], 'default', 'value' => function () {
                $fileArr = explode('.', $this->file->name);
                $ext = $this->file->getExtension();
                array_pop($fileArr);
                $nameNoExt = implode('.', $fileArr);
                $replace = [
                    '{hash}' => md5($this->_microtime . $this->file->name),
                    '{ext}' => $ext,
                    '{name}' => $nameNoExt,
                    '{filename}' => $this->file->name
                ];
                $filename = strtr($this->filenameFormat, $replace);
                return $filename;
            }],
            [['size'], 'integer'],
            ['group', 'string', 'max' => 20],
            ['ext', 'string', 'max' => 20],
            ['hash', 'string', 'max' => 100],
            [['name'], 'string', 'max' => 256],
            [['type'], 'string', 'max' => 64],
            [['path'], 'string', 'max' => 100],
            [['filename'], 'string', 'max' => 256]
        ];
    }

    public function behaviors()
    {
        return [
            '\yii\behaviors\BlameableBehavior',
            '\yii\behaviors\TimestampBehavior'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Basename',
            'filename' => 'Filename',
            'size' => 'Filesize',
            'group' => 'Group',
            'type' => 'Content Type',
        ];
    }

    /**
     * @inheritDoc
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->file && $this->file instanceof UploadedFile) {
            $fullPath = Yihai::getAlias($this->path . $this->filename);
            FileHelper::createDirectory(dirname($fullPath));
            if ($this->saveCallback !== null) {
                return call_user_func($this->saveCallback, $this);
            } else {
                return $this->file->saveAs($fullPath, false);
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inherited
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            @unlink(Yihai::getAlias($this->path . $this->filename));
            return true;
        }
        return false;
    }

    /**
     * Save file
     * @param UploadedFile|string $file
     * @param array $options
     * @return boolean|static
     */
    public static function saveAs($file, $options = [])
    {
        if (is_string($file)) {
            $file = UploadedFile::getInstanceByName($file);
        }
        $options['file'] = $file;
        $model = new static($options);
        return $model->save() ? $model : false;
    }

    public function getContent()
    {
        return file_get_contents($this->getFullpath());
    }

    public function getPath()
    {
        return Yihai::getAlias($this->path);
    }

    public function getFullpath()
    {
        return Yihai::getAlias($this->path . $this->filename);
    }

    public function url($action = '', $system = false)
    {
        if ($action === '')
            $action = str_replace('_', '-', $this->group);
        $filename = str_replace('.', ':', $this->filename);
        $path = '/public/';
        if($system)
            $path = '/system/public/';
        return Url::to([$path . $action . '/' . $filename]);
    }
    public function urlFile($action = '', $system = false)
    {
        if ($action === '')
            $action = str_replace('_', '-', $this->group);
        $filename = str_replace('.', ':', $this->filename);
        $path = '/public/files/';
        if($system)
            $path = '/system/public/files/';
        return Url::to([$path . $action . '/' . $filename]);
    }

    public function getBase64()
    {
        return base64_encode($this->content);
    }
    public function getBase64_url()
    {
        return 'data:'.$this->type.';base64,'.$this->base64;
    }
}