<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension;


use Yihai;
use yihai\core\extension\elfinder\volume\Base;
use yihai\core\extension\elfinder\volume\Local;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class FileManager
 * @package yihai\core\extension
 * @property Base[] $volumes
 */
class FileManager extends Component
{
    /**
     * @var Base[]
     */
    private $_volumes = [];
    public $connectOptions = [];
    public $managerOptions = [];

    /**
     * @param array|Local $volume
     */
    public function addLocalVolume($volume = [])
    {
        if (is_array($volume)) {
            if (!isset($volume['class']))
                $volume['class'] = Local::class;
            try {
                $volume = Yihai::createObject($volume);
            } catch (InvalidConfigException $e) {
            }
        }
        $this->_volumes[$volume->id] = $volume;
    }

    /**
     * @param $id
     * @return null|Base
     */
    public function getVolume($id)
    {
        if (isset($this->_volumes[$id]))
            return $this->_volumes[$id];
        return null;
    }

    /**
     * @return Base[]
     */
    public function getVolumes()
    {
        ArrayHelper::multisort($this->_volumes, 'position');
        return $this->_volumes;
    }

    /**
     * @param array|Base[] $volumes
     */
    public function setVolumes($volumes)
    {
        foreach ($volumes as $volume) {
            if (is_array($volume)) {
                if (!isset($volume['class']))
                    $volume['class'] = Local::class;
                try {
                    $volume = Yihai::createObject($volume);
                } catch (InvalidConfigException $e) {
                }
            }
            $this->_volumes[$volume->id] = $volume;
        }
    }

}