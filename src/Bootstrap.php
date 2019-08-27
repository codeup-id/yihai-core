<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core;


use Yihai;
use yihai\core\base\Module;
use yii\base\InvalidConfigException;

class Bootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @var Module[]
     */
    private $_modules;

    /**
     * @param \yihai\core\console\Application|\yihai\core\web\Application $app
     * @return \yii\web\Response|void
     */
    private function settingsGet($app)
    {
        if(Yihai::$app->request->post('___settings')){
            $settings = Yihai::$app->request->post();
            unset($settings[Yihai::$app->request->csrfParam], $settings['___settings']);
            $languagePost = Yihai::$app->request->post('language');
                $cookies = $app->response->cookies;
                $app->language = $languagePost;
                $cookies->add(new \yii\web\Cookie([
                    'name' => '___settings',
                    'value' => $settings
                ]));
            return $app->response->refresh();

        }
        elseif($app->request->cookies->has('___settings'))
        {
            $settings = $app->request->cookies->getValue('___settings');
            if(isset($app->params['___settings']))
                $settings = array_merge($app->params['___settings'], $settings);
            $app->params['___settings'] = $settings;
            if(isset($settings['language']) && isset($app->params['languageList'][$settings['language']]))
                $app->language = $settings['language'];
        }
    }

    /**
     * @param \yihai\core\console\Application|\yihai\core\web\Application $app
     */
    public function bootstrap($app)
    {
        if (Yihai::$app instanceof \yii\web\Application) {
            Yihai::$app->theme->set();
            $this->settingsGet($app);
        }
        if ($app->modules) {
            foreach ($app->modules as $name => $config) {
                try {
                    $module = $app->getModule($name);
                }catch (InvalidConfigException $e){
                    if(is_dir(Yihai::getAlias('@yihai/modules/'.$name.'/src'))){
                        Yihai::setAlias('@yihai/modules/cat',Yihai::getAlias('@yihai/modules/'.$name.'/src'));
                    }
                    elseif(is_dir(Yihai::getAlias('@yihai/modules/module-'.$name.'/src'))){
                        Yihai::setAlias('@yihai/modules/cat',Yihai::getAlias('@yihai/modules/module-'.$name.'/src'));
                    }
                    elseif(is_dir(Yihai::getAlias('@yihai/modules/yihai-module-'.$name.'/src'))){
                        Yihai::setAlias('@yihai/modules/cat',Yihai::getAlias('@yihai/modules/yihai-module-'.$name.'/src'));
                    }
                    $module = $app->getModule($name);
                }

                if ($module instanceof \yihai\core\base\Module) {
                    $this->_modules[$name] = $module;
                    try {
                        $module->init_app_config($app);
                    }catch (\Exception $e){}
                }
            }
            foreach ($app->modules as $name => $config) {
                if(!isset($this->_modules[$name])) continue;
                $module = $this->_modules[$name];
                // memuat bootstrap jika tidak ada pada config
                if ($module instanceof \yihai\core\base\Module) {
                    if (!in_array($name, $app->bootstrap)) {
                        $app->bootstrap[] = $name;
                        Yihai::setAlias('@yihai-modules-' . $name, $module->getBasePath());
                        if (Yihai::$app instanceof \yihai\core\web\Application) {
                            $module->addMenu();
                        }
                        $module->bootstrap($app);
                    }
                }
            }
        }
        // SET CONTAINER
        Yihai::$container->set('yii\web\JqueryAsset', 'yihai\core\assets\JqueryAsset');
    }

}