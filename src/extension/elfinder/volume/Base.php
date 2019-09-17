<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */


namespace yihai\core\extension\elfinder\volume;

use elFinder;
use elFinderVolumeDriver;
use Yihai;
use yihai\core\models\DataModel;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;


/**
 * @property array $defaults
 */
class Base extends BaseObject
{

    public $id;
    /**
     * @var int
     */
    public $position = 0;
    public $driver = 'LocalFileSystem';

    public $name = 'Root';

    public $options = [];

    /**
     * @var array|string
     */
    public $access_read = '*';
    public $access_write = '*';
    /**
     * jika false, maka akan Dibuat pada directory asset bundle
     * @var string|bool
     */
    public $tmbPath = 'assets/thumbnails';

    public $plugin = [];

    public $path;

    public $baseUrl = '@web/public/files';

    public $basePath = '@yihai/storages';
    /**
     * plugin watermark config
     * @var array|bool
     */
    public $watermark = false;
    public $plugins = [];
    public $hide_quarantine = true;
    public $hide_tmb = true;
    public $attributes = [];
    public $group;
    public $withUserPath = false;
    /**
     * List of commands disabled on this root
     * @var array
     */
    public $disabled = [];

    public function init()
    {
        parent::init();
        if (!$this->id)
            $this->id = $this->name;
    }

    public function getAlias()
    {
        if (is_array($this->name)) {
            return Yihai::t($this->name['category'], $this->name['message']);
        }

        return $this->name;
    }

    public function isAvailable()
    {
        return $this->defaults['read'];
    }

    private $_defaults;

    public function getDefaults()
    {
        if ($this->_defaults !== null)
            return $this->_defaults;
        $this->_defaults['read'] = false;
        $this->_defaults['write'] = false;

        if (!empty($this->access_write)) {
            if (is_string($this->access_write)) {
                if($this->access_write === '*')
                    $this->_defaults['write'] = true;
                else
                    $this->_defaults['write'] = Yihai::$app->user->can($this->access_write);
            }
            elseif (is_array($this->access_write)) {
                foreach ($this->access_write as $role) {
                    if (Yihai::$app->user->can($role)) {
                        $this->_defaults['write'] = true;
                        break;
                    }
                }
            }
        }
        if ($this->_defaults['write']) {
            $this->_defaults['read'] = true;
        } elseif (!empty($this->access_read)) {
            if (is_string($this->access_read)) {
                if($this->access_read === '*')
                    $this->_defaults['read'] = true;
                else
                    $this->_defaults['read'] = Yihai::$app->user->can($this->access_read);
            }
            elseif (is_array($this->access_read)) {
                foreach ($this->access_read as $role) {
                    if (Yihai::$app->user->can($role)) {
                        $this->_defaults['read'] = true;
                        break;
                    }
                }
            }
        }

        return $this->_defaults;
    }

    protected function optionsModifier($options)
    {
        return $options;
    }

    /**
     * @return array
     * @throws \yii\base\Exception
     */
    public function getRoot()
    {

        if(!$this->group){
            $this->group = $this->path;
        }
        $options['id'] = $this->id;
        $options['yihaiPathAlias'] = $this->basePath;
        $options['yihaiGroup'] = $this->group;

        $options['driver'] = $this->driver;
        $options['plugin'] = $this->plugin;
        $options['defaults'] = $this->getDefaults();
        $options['alias'] = $this->getAlias();

        $options['tmpPath'] = Yihai::getAlias('@runtime/elFinderTmpPath');
        if ($this->tmbPath) {
            $this->tmbPath = trim($this->tmbPath, '/');
            $options['tmbPath'] = Yihai::getAlias('@webroot/' . $this->tmbPath);
            $options['tmbURL'] = Yihai::$app->request->hostInfo . Yihai::getAlias('@web/' . $this->tmbPath);
        } else {
            $subPath = md5($this->className() . '|' . serialize($this->name));
            $options['tmbPath'] = Yihai::$app->assetManager->getPublishedPath(__DIR__) . DIRECTORY_SEPARATOR . $subPath;
            $options['tmbURL'] = Yihai::$app->request->hostInfo . Yihai::$app->assetManager->getPublishedUrl(__DIR__) . '/' . $subPath;
        }

        FileHelper::createDirectory($options['tmbPath']);


        $options['mimeDetect'] = 'internal';
        $options['imgLib'] = 'auto';
        $this->attributes[] = [
            'pattern' => '#.*(\.git(.*)$)#i',
            'read' => false,
            'write' => false,
            'hidden' => true,
            'locked' => false
        ];
        if($this->hide_tmb){
            $this->attributes[] = [
                'pattern' => '#.*(\.tmb)$#i',
                'read' => false,
                'write' => false,
                'hidden' => true,
                'locked' => false
            ];
        }
        if($this->hide_quarantine){
            $this->attributes[] = [
                'pattern' => '#.*(\.quarantine)$#i',
                'read' => false,
                'write' => false,
                'hidden' => true,
                'locked' => false
            ];
        }
        $options['attributes'] = $this->attributes;
        if(!empty($this->disabled))
            $options['disabled'] = $this->disabled;
        if (is_array($this->watermark)) {
            Yihai::$app->fileManager->connectOptions['bind']['upload.presave'] = 'Plugin.Watermark.onUpLoadPreSave';
            $this->plugin['Watermark'] = $this->watermark;
        } elseif (is_bool($this->watermark) && !$this->watermark) {
            $this->plugin['Watermark'] = ['enable' => false];
        }
        $options['plugin'] = $this->plugin;

        $options = $this->optionsModifier($options);
        return ArrayHelper::merge($options, $this->options);
    }

}