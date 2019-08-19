<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */


namespace yihai\core\extension\elfinder;
use yihai\core\extension\elfinder\volume\Local;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use Yihai;

class PathController extends BaseController{
	public $disabledCommands = ['netmount'];
	public $root = [];
	public $watermark;

	private $_options;

	public function getOptions()
	{
		if($this->_options !== null)
			return $this->_options;

		$subPath = Yihai::$app->request->getQueryParam('path', '');

		$this->_options['roots'] = [];

		$root = $this->root;

		if(is_string($root))
			$root = ['path' => $root];

		if(!isset($root['class']))
			$root['class'] = Local::class;

		if(!isset($root['path']))
			$root['path'] = '';

		if(!empty($subPath)){
			if(preg_match("/\./i", $subPath)){
				$root['path'] = rtrim($root['path'], '/');
			}
			else{
				$root['path'] = rtrim($root['path'], '/');
				$root['path'] .= '/' . trim($subPath, '/');
			}
		}

		$root = Yihai::createObject($root);

		/** @var Local $root*/

		if($root->isAvailable())
			$this->_options['roots'][] = $root->getRoot();

		if(!empty($this->watermark)){
			$this->_options['bind']['upload.presave'] = 'Plugin.Watermark.onUpLoadPreSave';

			if(is_string($this->watermark)){
				$watermark = [
					'source' => $this->watermark
				];
			}else{
				$watermark = $this->watermark;
			}

			$this->_options['plugin']['Watermark'] = $watermark;
		}

		$this->_options = ArrayHelper::merge($this->_options, $this->connectOptions);

		return $this->_options;
	}

	public function getManagerOptions(){
		$options = parent::getManagerOptions();
		$options['url'] = Url::toRoute(['connect', 'path' => Yihai::$app->request->getQueryParam('path', '')]);
		return $options;
	}
} 