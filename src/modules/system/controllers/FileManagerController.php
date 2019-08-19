<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system\controllers;


use elFinder;
use elFinderVolumeDriver;
use Yihai;
use yihai\core\extension\elfinder\BaseController;
use yihai\core\extension\elfinder\plugin\Sluggable;
use yihai\core\extension\elfinder\volume\Base;
use yihai\core\models\DataModel;
use yihai\core\models\SysUploadedFiles;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class FileManagerController extends BaseController
{
    /**
     * @var Base[]
     */
    private $volumes = [];
    public $disabledCommands = ['netmount'];
    private $_options;

    public function getOptions()
    {
        if($this->_options !== null)
            return $this->_options;

        $this->_options['roots'] = [];

        $this->volumes = Yihai::$app->fileManager->getVolumes();
        foreach($this->volumes as $volume){
            if($volume->isAvailable()) {
                $this->_options['roots'][] = $root = $volume->getRoot();
                if(isset($root['plugin']) && isset($root['plugin']['Sluggable'])){
                    $this->plugin['Sluggable'] = [
                        'class' => Sluggable::class
                    ];
                }
            }
        }
        $this->connectOptions = ArrayHelper::merge([
            'bind' => [
//                'open' => [function ($cmd, &$result, $args, $elfinder) {
//                    foreach($result['files'] as $i => $file) {
//                        $result['files'][$i]['extra'] = 'Extra data of '.$file['name'];
//                    }
//                    print_r($result);exit;
//                }],
//                'upload' => [
//                    function($cmd, &$result, $args, $elfinder,\elFinderVolumeLocalFileSystem $volume) {
//                        foreach ($result['added'] as $item) {
//                            $path = $volume->getOption('path');
//                            $path = str_replace(Yihai::getAlias('@yihai'), '@yihai', $path);
//                            $path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
//                            $sysUpload = new SysUploadedFiles();
//                            $sysUpload->path = $path;
//                            $sysUpload->group = $volume->getOption('yihaiGroup');
//                            $sysUpload->filename=$item['name'];
//                            $sysUpload->name = $item['name'];
//                            $sysUpload->type = $item['mime'];
//                            $sysUpload->hash = md5(microtime().$item['name']);
//                            $sysUpload->size = $item['size'];
//                            $sysUpload->save(false);
//
//                        }
//                    }
//                ]
            ]
        ],$this->connectOptions);
        $this->connectOptions = ArrayHelper::merge($this->connectOptions, Yihai::$app->fileManager->connectOptions);
        $this->_options = ArrayHelper::merge($this->_options, $this->connectOptions);

        return $this->_options;
    }
}