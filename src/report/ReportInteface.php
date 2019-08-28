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
     * Default Report key
     * @return string
     */
    public static function defaultKey();
    /**
     * Default Report description
     * @return string
     */
    public static function defaultDesc();

    /**
     * Default Report Template
     * @return string
     */
    public static function defaultReportHtml();

    /**
     * list query
     * @return ActiveQuery[]
     */
    public function dbQueries();

    /**
     * Rule untuk filter search
     * @return array
     */
    public function filterRules();

    /**
     * saat menerima form filter
     * contoh:
     * ```php
     *  if($filterModel->filterRuleAttribute){
     *      $query->andWhere(['table_field' => $filterModel->filterRuleAttribute]);
     *  }
     * ```
     * @param ActiveQuery[] $query
     * @param FilterModel $filterModel
     * @return mixed
     */
    public function filterOnFilter(&$query, $filterModel);

    /**
     * filter html form
     * @param ActiveForm $form
     * @return void
     */
    public function filterHtml($form);

    /**
     * field yang tersedia ditamplkan pada template editor
     * ```php
     *   return [
     *      'lists' => [
     *          '...' => ['...','...']
     *      ],
     *      'global' => .....
     *   ];
     * ```
     * @return array
     */
    public function availableFields();

    /**
     * data lists
     * ```php
     *      return [
     *          'lists' => [
     *              'key' => $this->dataQuery('key_query')
     *          ]
     *      ];
     * ```
     * @return mixed
     */
    public function dataVars();

}