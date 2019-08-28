<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\i18n;


use Yihai;

class PhpMessageSource extends \yii\i18n\PhpMessageSource
{
    public $pathMap = [];

    protected function loadMessages($category, $language)
    {
        $messages = parent::loadMessages($category, $language);
        foreach ($this->pathMap as $pathMap) {
            $pathMap = Yihai::getAlias($pathMap);
            if (!is_dir($pathMap)) continue;
            $fileMap = $pathMap . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $category . '.php';
            if (is_file($fileMap)) {
                $messages = array_merge($messages, include $fileMap);
            }
        }
        return $messages;
    }
}