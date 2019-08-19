<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\extension\tinymce;

use Yihai;
use yihai\core\theming\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class TinyMce extends InputWidget
{
    /**
     * @var string the language to use. Defaults to null (en).
     */
    public $language;
    /**
     * @var array the options for the TinyMCE JS plugin.
     * Please refer to the TinyMCE JS plugin Web page for possible options.
     * @see http://www.tinymce.com/wiki.php/Configuration
     */
    public $clientOptions = [];
    /**
     * @var bool whether to set the on change event for the editor. This is required to be able to validate data.
     */
    public $triggerSaveOnBeforeValidateForm = true;

    public $fileManagerUrl;

    public $preset = 'full';
    public $useFilePicker = true;

    public $mobileTheme = false;

    /**
     * @var bool|array
     */
    public $elfinderPathId = false;
    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
        if(!$this->language){
            $this->language = Yihai::$app->language;
        }
        switch ($this->preset) {
            case 'full':
                $this->presetFull();
                break;
        }
        if($this->useFilePicker){
            $this->clientOptions['file_picker_callback'] = new \yii\web\JsExpression('tinyMceElFinderBrowser');
        }
        if($this->mobileTheme === false){
            $this->clientOptions['mobile'] = ['theme'=>'silver'];
        }
        $this->registerClientScript();
    }

    protected function presetFull()
    {
        $this->clientOptions['plugins'] = 'print preview searchreplace autolink directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help noneditable';
        $this->clientOptions['toolbar'] = "undo redo | styleselect font | bold italic strikethrough underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat";
    }

    /**
     * Registers tinyMCE js plugin
     */
    protected function registerClientScript()
    {
        $js = [];
        $view = $this->getView();

        TinyMceAsset::register($view);
        $id = $this->options['id'];

        $this->clientOptions['selector'] = "#$id";
        // @codeCoverageIgnoreStart
        if ($this->language !== 'en') {
            $langFile = "{$this->language}.js";
            $langAssetBundle = TinyMceLangAsset::register($view);
            $langAssetBundle->js[] = $langFile;
            $this->clientOptions['language_url'] = $langAssetBundle->baseUrl . "/{$langFile}";
            $this->clientOptions['language'] = "{$this->language}";//Language fix. Without it EN language when add some plugins like codemirror 
        }
        // @codeCoverageIgnoreEnd

        $options = Json::encode($this->clientOptions);
        $js[] = 'tinymce.remove("#' . $id . '");';
        $js[] = "tinymce.init($options);";
        if ($this->triggerSaveOnBeforeValidateForm) {
            $js[] = "$('#{$id}').parents('form').on('beforeValidate', function() { tinymce.triggerSave(); });";
        }
        $view->registerJs(implode("\n", $js));
        $this->registerElfinder();
    }

    protected function registerElfinder()
    {

        $view = $this->getView();
        $elPathId = '';
        if(is_array($this->elfinderPathId)){
            $elPathId = '&pathId='.implode(',',$this->elfinderPathId);
        }
        $view->registerJs('function tinyMceElFinderBrowser (callback, value, meta) {
			
			tinymce.activeEditor.windowManager.openUrl({
				url: \'' . \yii\helpers\Url::to(['/system/file-manager/manager?tinymce'.$elPathId]) . '\',
				title: \'' . Yihai::t('yihai', 'Browse File') . '\',
				width: jQuery(window).width()/1.2,
				height: jQuery(window).height()/1.2,
				buttons: [
				{
                      type: \'cancel\',
                      name: \'cancel\',
                      text: \'' . Yihai::t('yihai', 'Cancel') . '\',
                      disabled: false,
                      primary: false,
                      align: \'end\'
                    }]
			});

			// @todo use official API but there seems no way to pass callback to TinyMCE 5 RC1 currently.
			tinymce.activeEditor.windowManager._elfinderCallBack = function (file, fm) {
				var url, reg, info;

				// URL normalization
				url = fm.convAbsUrl(file.url);
				
				// Make file info
				info = file.name + \' (\' + fm.formatSize(file.size) + \')\';

				// Provide file and text for the link dialog
				if (meta.filetype == \'file\') {
					callback(url, {text: info, title: info});
				}

				// Provide image and alt text for the image dialog
				if (meta.filetype == \'image\') {
					callback(url, {alt: info});
				}

				// Provide alternative source and posted for the media dialog
				if (meta.filetype == \'media\') {
					callback(url);
				}
			};

			// set CSS for elFinder dialog
			try {
				myIfm = document.querySelector(\'div.tox-dialog__body-content > div > iframe.elfinder-manager\');
				if (myIfm) {
					pStyle = myIfm.parentElement.style;
					pStyle.height = \'100%\';
					pStyle.overflow = \'hidden\';
					// hide footer
					myIfm.parentElement.parentElement.parentElement.parentElement.nextElementSibling.style.display = \'none\';
				}
			} catch(e) {}
			
			return false;
		}

');
    }
}
