<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\helpers;


use Yihai;
use yihai\core\console\Application;

class Url extends \yii\helpers\Url
{
    public static function toRest($controller = '', $action = 'index', $params = [], $scheme = false)
    {
        $controller = rtrim($controller, '/') . '/';
        $url = [$controller . '__rest/' . $action];
        $url = array_merge($url, $params);
        return parent::to($url, $scheme);
    }

    public static function moduleUrl($path)
    {
        $path = '/'.ltrim($path, '/');
        if(!Yihai::$app->controller) return $path;
        return '/'.Yihai::$app->controller->module->id.$path;
    }

    /**
     * @param $str
     * @param bool $relative_path
     * @return string
     */
    public static function sanitize_filename($str, $relative_path = FALSE)
    {
        $bad = array(
            '../', '<!--', '-->', '<', '>',
            "'", '"', '&', '$', '#',
            '{', '}', '[', ']', '=',
            ';', '?', '%20', '%22',
            '%3c',        // <
            '%253c',    // <
            '%3e',        // >
            '%0e',        // >
            '%28',        // (
            '%29',        // )
            '%2528',    // (
            '%26',        // &
            '%24',        // $
            '%3f',        // ?
            '%3b',        // ;
            '%3d'        // =
        );
        if (!$relative_path) {
            $bad[] = './';
            $bad[] = '/';
        }
        $str = static::remove_invisible_characters($str, FALSE);
        return stripslashes(str_replace($bad, '', $str));
    }

    /**
     * @param $str
     * @param bool $url_encoded
     * @return null|string|string[]
     */
    public static function remove_invisible_characters($str, $url_encoded = TRUE)
    {
        $non_displayables = array();
        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/';    // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';    // url encoded 16-31
        }
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127
        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);
        return $str;
    }

    /**
     * @return array
     */
    public static function getKontenReplacing()
    {
        $replace = [];
        if(!Yihai::$app instanceof Application){
            $replace['{{==@web}}'] = Url::to('@web', true);
        }
        return $replace;
    }

    public static function imageBase64($mime, $data)
    {
        return 'data:'.$mime.';base64,'.$data;
    }

}