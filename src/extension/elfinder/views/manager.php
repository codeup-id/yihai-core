<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

/**
 * @var \yihai\core\web\View $this
 * @var array $options

 */

use yihai\core\extension\elfinder\Assets;
use yii\helpers\Json;

$this->title = 'FileManager';
Assets::register($this);
Assets::addLangFile($options['lang'], $this);

if(!empty($options['noConflict']))
	Assets::noConflict($this);

unset($options['noConflict']);
$options['soundPath'] = Assets::getSoundPathUrl();
$options['i18nBaseUrl'] = Assets::getI18nPathUrl();
$options['height'] = '100%';
$options['startPathHash'] = 'yihai_';
$options['cssAutoLoad'] = []; //disable theme
$options['themes'] = Assets::getThemesListManifest();

$this->registerJs("
function ElFinderGetCommands(disabled){
    var Commands = elFinder.prototype._options.commands;
    if (jQuery.inArray('*', Commands) === 0) {
        Commands = Object.keys(elFinder.prototype.commands);
    }
    jQuery.each(disabled, function(i, cmd) {
        (idx = jQuery.inArray(cmd, Commands)) !== -1 && Commands.splice(idx,1);
    });
    return Commands;
}

    var winHashOld = '';
    var _elfinder = jQuery('#elfinder').elfinder(".Json::encode($options).").elfinder('instance');
    function elFinderFullScreen(){

        var width = jQuery(window).width()-(jQuery('#elfinder').outerWidth(true) - jQuery('#elfinder').width());
        var height = jQuery(window).height()-(jQuery('#elfinder').outerHeight(true) - jQuery('#elfinder').height());

        var el = jQuery('#elfinder').elfinder('instance');

        var winhash = jQuery(window).width() + '|' + jQuery(window).height();


        if(winHashOld == winhash)
            return;

        winHashOld = winhash;

        el.resize(width, height);
    }
    jQuery(window).resize(elFinderFullScreen);
    elFinderFullScreen();
    "/*, \yii\web\View::POS_LOAD*/);


$this->registerCss("
html, body {
    height: 100%;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    position: relative;
    padding: 0; margin: 0;
}
");


echo '<div id="elfinder"></div>';

