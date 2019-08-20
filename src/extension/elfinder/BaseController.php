<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\elfinder;
use Yihai;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yihai\core\web\Controller;
use yii\web\JsExpression;


class BaseController extends Controller{
    public $layout = '@yihai/views/_layouts/blank-content';
	public $access = ['@'];
	public $user = 'user';
	public $managerOptions = [];
	public $connectOptions = [];
	public $plugin = [];

	public function behaviors()
	{
		return [
			'access' => [
				'user' => $this->user,
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => $this->access,
					],
				],
			],
		];
	}
	public function getViewPath()
    {
        return __DIR__.'/views';
    }

    public function getOptions(){
		return $this->connectOptions;
	}

	public function getManagerOptions($pathId = ''){
		$options = [
			'url'=> Url::toRoute(['connect', 'pathId'=>$pathId]),
			'customData' => [
                Yihai::$app->request->csrfParam => Yihai::$app->request->csrfToken
			],
			'resizable' => false
		];


        if(isset($_GET['tinymce'])){
            $options['getFileCallback'] = new JsExpression('function(file, fm) { '.
                'parent.tinymce.activeEditor.windowManager.getParams().oninsert(file, fm);'.
                'parent.tinymce.activeEditor.windowManager.close();}');
        }

		if(isset($_GET['filter'])){
			if(is_array($_GET['filter']))
				$options['onlyMimes'] = $_GET['filter'];
			else
				$options['onlyMimes'] = [$_GET['filter']];
		}

		if(isset($_GET['lang']))
			$options['lang'] = $_GET['lang'];

		if(isset($_GET['callback'])){
			if(isset($_GET['multiple']))
				$options['commandsOptions']['getfile']['multiple'] = true;

			$options['getFileCallback'] = new JsExpression('function(file){ '.
				'if (window!=window.top) {var parent = window.parent;}else{var parent = window.opener;}'.
				'if(parent.codeup.elFinder.callFunction('.Json::encode($_GET['callback']).', file))'.
				'window.close(); }');
		}

		if(!isset($options['lang']))
			$options['lang'] = ElFinder::getSupportedLanguage(Yihai::$app->language);

		if(!empty($this->disabledCommands))
			$options['commands'] = new JsExpression('ElFinderGetCommands('.Json::encode($this->disabledCommands).')');

        if(isset($this->managerOptions['handlers'])) {
            $handlers = [];
            foreach ($this->managerOptions['handlers'] as $event => $js) {
                $handlers[$event] = new JsExpression($js);
            }
            $this->managerOptions['handlers'] = $handlers;
        }

		return ArrayHelper::merge($options, $this->managerOptions);
	}

    public function actionConnect($pathId = ''){
	    $options = $this->getOptions();
	    if($pathId) {
            $pathId = explode(',', $pathId);
            if ($pathId && isset($options['roots']) && is_array($options['roots'])) {
                foreach ($options['roots'] as $i => $root) {
                    if (!in_array($root['id'], $pathId)) {
                        unset($options['roots'][$i]);
                    }
                }
            }
        }
        return $this->renderFile(__DIR__."/views/connect.php", ['options'=>$options, 'plugin' => $this->plugin]);
    }

    public function actionIndex()
    {
        $this->layout = '@yihai/views/_layouts/backend';
        return $this->render("manager", ['options'=>$this->getManagerOptions()]);
    }
	public function actionManager($pathId = ''){
		return $this->render("manager", ['options'=>$this->getManagerOptions($pathId)]);
	}
} 
