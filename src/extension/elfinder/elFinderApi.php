<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\elfinder;

class elFinderApi extends \elFinder
{
    public function __construct($opts, $plugins=[]) {
        parent::__construct($opts);

        $_req = $_SERVER["REQUEST_METHOD"] == 'POST' ? $_POST : $_GET;
        $_reqCmd = isset($_req['cmd']) ? $_req['cmd'] : '';

        foreach($plugins as $plugin){
            /** @var PluginInterface $plugin */
            $plugin = \Yihai::createObject($plugin);

            foreach ($plugin->bind as $cmd => $methods) {
                $doRegist = (strpos($cmd, '*') !== false);
                if (! $doRegist) {
                    $_getcmd = function($cmd){
                        list($ret) = explode('.', $cmd);
                        return trim($ret);
                    };
                    $doRegist = ($_reqCmd && in_array($_reqCmd, array_map($_getcmd, explode(' ', $cmd))));
                }
                if ($doRegist) {
                    if (! is_array($methods))
                        $methods = array($methods);

                    foreach($methods as $method){
                        if ($method && method_exists($plugin, $method)) {
                            if(!isset($this->plugins[$plugin->getName()]))
                                $this->plugins[$plugin->getName()] = $plugin;

                            $this->bind($cmd, array($this->plugins[$plugin->getName()], $method));
                        }
                    }
                }

            }

        }
    }
}