<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */


namespace yihai\core\extension\elfinder;

abstract class PluginInterface
{
    /**
     * @return string
     */
    abstract public function getName();

    public $bind = [];

    public $enable = true;

    /**
     * @param $name string
     * @param $volume \elFinderVolumeDriver
     * @return mixed
     */
    protected function getOption($name, $volume) {
        if (is_object($volume)) {
            $volumeOptions = $volume->getOptionsPlugin($this->getName());
            if (isset($volumeOptions[$name])) {
                return $volumeOptions[$name];
            }
        }
        return $this->$name;
    }

    /**
     * @param $volume \elFinderVolumeDriver
     * @return bool
     */
    public function isEnable($volume){
        return $this->getOption('enable', $volume);
    }
}