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
                    'year_full' => date('Y', time()),
                    'year_two_digit' => date('y', time()),
                    'month_digit_two' => date('m', time()),
                    'month_digit_nozero' => date('n', time()),
                    'month_name_three' => Yihai::$app->formatter->asDate(time(), 'php:M'),
                    'month_name_full' => Yihai::$app->formatter->asDate(time(), 'php:F'),
                    'month_number_day' => date('t', time()),
                    'day_digit_two' => date('d', time()),
                    'day_digit_nozero' => date('j', time()),
                    'day_digit_week' => Yihai::$app->formatter->asDate(time(), 'php:N'),
                    'day_digit_year' => Yihai::$app->formatter->asDate(time(), 'php:z'),
                    'day_name_three' => Yihai::$app->formatter->asDate(time(), 'php:D'),
                    'day_name_full' => Yihai::$app->formatter->asDate(time(), 'php:l'),
                    'time_am_or_pm' => Yihai::$app->formatter->asDate(time(), 'php:A'),
                    'hour_digit_12' => Yihai::$app->formatter->asDate(time(), 'php:h'),
                    'hour_digit_12_nozero' => Yihai::$app->formatter->asDate(time(), 'php:g'),
                    'hour_digit_24' => Yihai::$app->formatter->asDate(time(), 'php:H'),
                    'hour_digit_24_nozero' => Yihai::$app->formatter->asDate(time(), 'php:G'),
                    'minutes' => Yihai::$app->formatter->asDate(time(), 'php:i'),
                    'seconds' => Yihai::$app->formatter->asDate(time(), 'php:s'),
                    'timezone_identifier' => Yihai::$app->formatter->asDate(time(), 'php:e'),
                    'timezone_hours' => Yihai::$app->formatter->asDate(time(), 'php:P'),
                    'timezone_abbreviation' => Yihai::$app->formatter->asDate(time(), 'php:T'),
                    'full_datetime_iso' => Yihai::$app->formatter->asDate(time(), 'php:c'),
                    'full_datetime_formatted' => Yihai::$app->formatter->asDate(time(), 'php:r'),
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

    public function availableFields()
    {
        $dataVars = $this->dataVars();
        return [
            'global' => $dataVars['global']
        ];
    }

    public function formatters()
    {
        return [
            'datetime' => [
                'year_full' => function ($value) { return date('Y', $value);},
                'year_two_digit' => function ($value) { return date('y', $value);},
                'month_digit_two' => function ($value) { return date('m', $value);},
                'month_digit_nozero' => function ($value) { return date('n', $value);},
                'month_name_three' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:M');},
                'month_name_full' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:F');},
                'month_number_day' => function ($value) { return date('t', $value);},
                'day_digit_two' => function ($value) { return date('d', $value);},
                'day_digit_nozero' => function ($value) { return date('j', $value);},
                'day_digit_week' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:N');},
                'day_digit_year' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:z');},
                'day_name_three' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:D');},
                'day_name_full' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:l');},
                'time_am_or_pm' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:A');},
                'hour_digit_12' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:h');},
                'hour_digit_12_nozero' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:g');},
                'hour_digit_24' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:H');},
                'hour_digit_24_nozero' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:G');},
                'minutes' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:i');},
                'seconds' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:s');},
                'timezone_identifier' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:e');},
                'timezone_hours' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:P');},
                'timezone_abbreviation' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:T');},
                'full_datetime_iso' => function ($value) { return Yihai::$app->formatter->asDate($value, 'php:c');},
                'full_datetime_formatted' => function ($value) {
                    return Yihai::$app->formatter->asDate($value, 'php:r');
                },
            ],
            'user' => [
                'username_from_ID'=>function($value){
                    return Yihai::$app->formatter->asUsername($value);
                }
            ]
        ];
    }
}