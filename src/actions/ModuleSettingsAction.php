<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\actions;


use Yihai;
use yihai\core\base\Action;
use yihai\core\helpers\Url;
use yihai\core\models\SysSettings;
use yihai\core\models\SysUploadedFiles;
use yihai\core\theming\Alert;
use yii\db\BaseActiveRecord;
use yii\db\StaleObjectException;
use yii\web\UploadedFile;

class ModuleSettingsAction extends Action
{
    public $module;
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $setting = Yihai::$app->settings->getModuleSetting($this->module);
        if ($setting->load(Yihai::$app->request->post()) && $setting->validate()) {
            foreach ($setting->attributes() as $attribute) {
                $sysSetting = SysSettings::findOne(['key' => $attribute, 'module' => $setting->getModuleId()]);
                $sysSetting->key = $attribute;
                if ($setting->fieldType($attribute) === $setting::FIELD_IMAGE) {
                    if ($file = UploadedFile::getInstance($setting, $attribute)) {
                        if ($oldFile = SysUploadedFiles::findOne(['id' => $sysSetting->value])) {
                            try {
                                $oldFile->delete();
                            } catch (StaleObjectException $e) {
                            } catch (\Throwable $e) {
                            }
                        }
                        if ($fileSave = SysUploadedFiles::saveAs($file, [
                            'group' => 'settings',
                            'uploadPath' => '@yihai/storages/settings',
                        ])) {
                            $sysSetting->value = $fileSave->id;
                        }
                    }

                } elseif($setting->fieldType($attribute) === $setting::FIELD_HTML){
                    $sysSetting->value = str_replace(array_values(Url::getKontenReplacing()), array_keys(Url::getKontenReplacing()), $setting->{$attribute});
                }
                else {
                    $sysSetting->value = $setting->{$attribute};
                }
                $sysSetting->save(false);
            }

            Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Yihai::t('yihai', 'Module settings updated.'), true);
            return $this->controller->refresh();
        } else {
            $setting->loadSettings();
        }
        return $this->controller->render('module', [
            'model' => $setting
        ]);
    }

}