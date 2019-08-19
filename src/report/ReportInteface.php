<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\report;


use yihai\core\base\FilterModel;
use yihai\core\theming\ActiveForm;
use yii\db\ActiveQuery;

interface ReportInteface
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
    /**
     * @return ActiveQuery[]
     */
    public function dbQueries();

    /**
     * Rule untuk filter search
     * @return array
     */
    public function filterRules();

    /**
     * @param ActiveQuery[] $query
     * @param FilterModel $filterModel
     * @return mixed
     */
    public function filterOnFilter(&$query, $filterModel);

    /**
     * @param ActiveForm $form
     * @return mixed
     */
    public function filterHtml($form);
    public function availableFields();
    public function dataVars();

}