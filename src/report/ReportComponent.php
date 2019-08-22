<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\report;


use Yihai;
use yihai\core\models\SysReports;
use yihai\core\models\UserModel;
use yihai\core\rbac\RbacHelper;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class ReportComponent
 * @package yihai\core\report
 * @property array $listReportClass;
 */
class ReportComponent extends Component
{
    /**
     * report class list for module
     * @var array
     */
    private $_modules = [];

    public $template_vars = [];

    /**
     * @param string $moduleId
     * @param string|IReport $class
     * @param array $roles
     * @param array|SysReports $data
     */
    public function saveReport($moduleId, $class, $roles, $data = [])
    {
        $data = ArrayHelper::merge([
            'key' => $class::defaultKey(),
            'desc' => $class::defaultDesc(),
            'module' => $moduleId,
            'class' => $class
        ], $data);
        try {
            if ($sysReportExist = SysReports::findOne(['module' => $moduleId, 'key' => $data['key']])) {
                $sysReport = $sysReportExist;
            } else {
                $sysReport = new SysReports();
            }

            foreach ($data as $d => $v) {
                if ($sysReport->canSetProperty($d)) {
                    $sysReport->{$d} = $v;
                }
            }
            $sysReport->is_sys = 1;
            if ($sysReport->save(false)) {
                $this->reportBuildRole($data['key'], $roles);
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }


    public function reportBuildRole($key, $roles = [])
    {
        if (empty($roles)) {
            $roles = [RbacHelper::roleRoleName('superuser')];
        }
        RbacHelper::addRoleCrud('/system/reports/build/', $roles, [$key]);
    }

    public function reportBuildRoleDelete($key)
    {
        RbacHelper::forceRemove(RbacHelper::menuRoleName('/system/reports/build/' . $key));
    }

    public function getListReportClass()
    {
        return ArrayHelper::map(SysReports::find()->select(['class'])->distinct('class')->all(), 'class', 'class');
    }

    public function dataVars()
    {
        $vars = [
            'global' => [
                'list' => [
                    '__no' => '{%__no%}'
                ],
                'datetime' => [
                    'full' => [
                        'iso' => Yihai::$app->formatter->asDate(time(), 'php:c'),
                        'formatted' => Yihai::$app->formatter->asDate(time(), 'php:r'),
                        'Y_m_d' => Yihai::$app->formatter->asDate(time(), 'php:Y-m-d'),
                        'Y_m_d_h_i_s' => Yihai::$app->formatter->asDate(time(), 'php:Y-m-d H:i:s'),
                    ],
                    'year'=>[
                        'full' => date('Y', time()),
                        'two_digit' => date('y', time()),
                    ],
                    'month' => [
                        'digit_two' => date('m', time()),
                        'digit_nozero' => date('n', time()),
                        'name_three' => Yihai::$app->formatter->asDate(time(), 'php:M'),
                        'name_full' => Yihai::$app->formatter->asDate(time(), 'php:F'),
                        'number_day' => date('t', time()),
                    ],
                    'day' => [
                        'digit_two' => date('d', time()),
                        'digit_nozero' => date('j', time()),
                        'digit_week' => Yihai::$app->formatter->asDate(time(), 'php:N'),
                        'digit_year' => Yihai::$app->formatter->asDate(time(), 'php:z'),
                        'name_three' => Yihai::$app->formatter->asDate(time(), 'php:D'),
                        'name_full' => Yihai::$app->formatter->asDate(time(), 'php:l'),
                    ],
                    'time' => [
                        'H_i_s' => Yihai::$app->formatter->asDate(time(), 'php:H:i:s'),
                        'am_or_pm' => Yihai::$app->formatter->asDate(time(), 'php:A'),
                        'hour_digit_12' => Yihai::$app->formatter->asDate(time(), 'php:h'),
                        'hour_digit_12_nozero' => Yihai::$app->formatter->asDate(time(), 'php:g'),
                        'hour_digit_24' => Yihai::$app->formatter->asDate(time(), 'php:H'),
                        'hour_digit_24_nozero' => Yihai::$app->formatter->asDate(time(), 'php:G'),
                        'minutes' => Yihai::$app->formatter->asDate(time(), 'php:i'),
                        'seconds' => Yihai::$app->formatter->asDate(time(), 'php:s'),
                    ],
                    'timezone'=>[ 
                        'timezone_identifier' => Yihai::$app->formatter->asDate(time(), 'php:e'),
                        'timezone_hours' => Yihai::$app->formatter->asDate(time(), 'php:P'),
                        'timezone_abbreviation' => Yihai::$app->formatter->asDate(time(), 'php:T'),
                    ],
                ],
                'user' => [
                    'id' => Yihai::$app->user->id,
                    'username' => Yihai::$app->user->identity->model->username,
                    'group' => Yihai::$app->user->identity->model->group
                ]
            ]
        ];
        return $vars;

    }

    /**
     * @return array
     */
    public function formatters()
    {
        return [

            'datetime' => [
                'full' => [
                    'iso' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:c');},
                    'formatted' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:r');},
                    'Y_m_d' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:Y-m-d');},
                    'Y_m_d_h_i_s' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:Y-m-d H:i:s');},
                ],
                'year'=>[
                    'full' => function ($value) { return date('Y', $value);},
                    'two_digit' => function ($value) { return date('y', $value);},
                ],
                'month' => [
                    'digit_two' => function ($value) { return date('m', $value);},
                    'digit_nozero' => function ($value) { return date('n', $value);},
                    'name_three' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:M');},
                    'name_full' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:F');},
                    'number_day' => function ($value) { return date('t', $value);},
                ],
                'day' => [
                    'digit_two' => function ($value) { return date('d', $value);},
                    'digit_nozero' => function ($value) { return date('j', $value);},
                    'digit_week' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:N');},
                    'digit_year' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:z');},
                    'name_three' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:D');},
                    'name_full' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:l');},
                ],
                'time' => [
                    'H_i_s' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:H:i:s');},
                    'am_or_pm' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:A');},
                    'hour_digit_12' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:h');},
                    'hour_digit_12_nozero' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:g');},
                    'hour_digit_24' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:H');},
                    'hour_digit_24_nozero' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:G');},
                    'minutes' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:i');},
                    'seconds' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:s');},
                ],
                'timezone'=>[
                    'timezone_identifier' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:e');},
                    'timezone_hours' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:P');},
                    'timezone_abbreviation' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:T');},
                ],
            ],
            'user' => [
                'to_username'=>function($value){
                    return Yihai::$app->formatter->asUsername($value);
                }
            ]
        ];
    }
}