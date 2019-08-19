<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\report;


use yihai\core\base\FilterModel;
use yii\db\ActiveQuery;

interface IReport
{
    /**
     * Report key
     * @return string
     */
    public static function defaultKey();
    /**
     * Report description
     * @return string
     */
    public static function defaultDesc();

    /**
     * Report Template
     * @return string
     */
    public static function defaultReportHtml();

}