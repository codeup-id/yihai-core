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
//        $core_file = Yihai::getAlias('@yihai-core/messages/' . $language . '/' . $category . '.php');
//        if (is_file($core_file))
//            return array_merge(include $core_file, $messages);
//        else {
//            if (isset($this->pathMap[$category])) {
//                $core_file = Yihai::getAlias($this->pathMap[$category] . "/$language/" . $category . '.php');
//                if (is_file($core_file))
//                    return array_merge(include $core_file, $messages);
//            }
//        }
        return $messages;
    }
}