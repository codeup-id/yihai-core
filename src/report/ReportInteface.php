<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\report;

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
     * @return \yii\db\ActiveQuery[]
     */
    public function dbQueries();

    /**
     * Rule untuk filter search
     * ```php
     *  return [
     *      [['id','name'], 'required],
     *      ['desc', 'safe']
     *  ];
     * ```
     * @return array
     */
    public function filterRules();

    /**
     * saat menerima form filter
     * "key_query" diambil dari key @see dbQueries()
     * contoh:
     * ```php
     *  if($filterModel->filterRuleAttribute){
     *      $query['key_query']->andWhere(['table_field' => $filterModel->filterRuleAttribute]);
     *  }
     * ```
     * @param \yii\db\ActiveQuery[] $query
     * @param \yihai\core\base\FilterModel $filterModel
     */
    public function filterOnFilter(&$query, $filterModel);

    /**
     * filter html form
     * ```php
     *  echo $form->field($filterModel, 'id');
     * ```
     * @param \yihai\core\theming\ActiveForm $form
     * @param \yihai\core\base\FilterModel $filterModel $this->filterModel
     * @return void
     */
    public function filterHtml($form, $filterModel);

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
     * "key_query" diambil dari key @see dbQueries()
     * ```php
     *      return [
     *          'lists' => [
     *              'key' => $this->dataQuery('key_query')
     *          ]
     *      ];
     * ```
     * @return array
     */
    public function dataVars();

}