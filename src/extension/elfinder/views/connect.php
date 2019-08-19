<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\extension\elfinder\Assets;
use yihai\core\extension\elfinder\elFinderApi;

/**
 * @var array $options
 * @var array $plugin
 */

define('ELFINDER_IMG_PARENT_URL', Assets::getPathUrl());

// run elFinder
$connector = new elFinderConnector(new elFinderApi($options, $plugin));
$connector->run();