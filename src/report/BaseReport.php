<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\report;

use Yihai;
use yihai\core\base\FilterModel;
use yihai\core\db\ActiveRecord;
use yihai\core\helpers\Url;
use yihai\core\models\SysReports;
use yihai\core\theming\ActiveForm;
use yihai\core\theming\Button;
use yihai\core\theming\Html;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yihai\core\helpers\ArrayHelper;

/**
 * Class BaseReport
 * @package yihai\core\report
 * @property string $desc
 * @property string $template
 * @property FilterModel $filterModel
 * @property string $filterModelName
 */
abstract class BaseReport extends BaseObject implements ReportInteface
{
    /** @var SysReports */
    public $model;

    private $_desc, $_template, $_template_render;
    /**
     * @var FilterModel
     */
    private $_filterModel;
    /**
     * list queries
     * @var ActiveQuery[]
     */
    private $_db_queries;
    /**
     * list data
     * @var array
     */
    private $_data_vars = [];
    /**
     * template telah di build
     * @var bool
     */
    private $_hasBuild = false;

    public function __construct($config = [])
    {
        if (!isset($config['model'])) {
            throw new InvalidConfigException('"model" property must set.');
        }
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        Yihai::$app->formatter->nullDisplay = '';
        $this->_desc = $this->model->desc ? $this->model->desc : static::defaultDesc();
        $this->_template = $this->model->template ? $this->model->template : static::defaultReportHtml();
        $this->normalizeTemplete();
        if ($this->filterRules()) {
            $filterModel = FilterModel::newFromRules($this->filterRules());
            $filterModel->setFormName($this->filterModelName);
            $this->_filterModel = $filterModel;
        }
    }

    public function normalizeTemplete()
    {
        $this->_template = preg_replace('/<script(.*?)>(.*?)<\/script>/is', '', $this->template);
    }

    public function build()
    {
        $this->_db_queries = $this->dbQueries();
        $filterModel = $this->filterModel;
        $this->_template_render = $this->template;
        if ($filterModel->load(Yihai::$app->request->post(), $this->filterModelName)) {
            $this->filterOnFilter($this->_db_queries, $filterModel);
            $comp = Yihai::$app->reports->dataVars();
            $comp['global']['filter'] = $this->filterModel;
            $this->_data_vars = ArrayHelper::merge($comp, $this->dataVars());
            $this->_template_render = strtr($this->_template_render, [
                '<!-- pagebreak -->' => '<div class="page-break-always"></div>'
            ]);
            $this->buildTemplateConditions($this->_template_render);
            $this->buildTemplateLang();
            $this->buildTemplateGlobalVars();
            $this->buildTemplateDataList($this->_template_render);
            $this->_template_render = strtr($this->_template_render, Url::getKontenReplacing());
            $this->_hasBuild = true;
        }


    }


    /**
     * @return string
     */
    public function getTemplateRender()
    {
        return $this->_template_render;
    }


    /**
     * mengambil data dari $data_var
     * @param ActiveRecord|array $data
     * @param $key
     * @param string $type lists|global
     * @return string
     */
    protected function dataGet($data, $key, $type = 'lists')
    {
        $formatter = explode(':', $key);
        $key = $formatter[0];
        unset($formatter[0]);
        if($type === 'lists')
        $defaultKey = '{%' . $key . '%}';
        else
        $defaultKey = '{' . $key . '}';
        try {
            if($type === 'lists') {
                if (is_array($data) && !ArrayHelper::keyExists($key, $data))
                    return $defaultKey;
                elseif ($data instanceof ActiveRecord && (count(explode('.', $key)) == 1) && !$data->canGetProperty($key))
                    return $defaultKey;
            }
            $value = ArrayHelper::getValue($data, $key, null);
            if (!empty($formatter)) {
                $allFormatter = $this->formatters();
                foreach ($formatter as $item) {
                    if (($f = ArrayHelper::getValue($allFormatter, $item)) && is_callable($f)) {
                        $value = call_user_func($f, $value);
                    }
                }
            }
            return $value;
        } catch (\Exception $e) {
            return $defaultKey;
        }
    }



    /**
     * cek template telah di build
     * @return bool
     */
    public function isHasBuild()
    {
        return $this->_hasBuild;
    }

    /**
     * @param string $template
     * @return array
     */
    protected function checkPatternDataList($template)
    {
        preg_match_all('/<!--%datalist:(.*)%-->(.*)?<!--%end_datalist:\1%-->/siU', $template, $matchDataList);
        return $matchDataList;
    }

    /**
     * @param string $data
     * @param string $template
     * @return string
     */
    protected function buildTemplateDataListData($data, $template)
    {
        if (!isset($this->_data_vars['lists'])) return $template;
        if (!isset($this->_data_vars['lists'][$data])) return $template;
        $render = '';
        $no = 1;
        foreach ($this->_data_vars['lists'][$data] as $data) {
            $render .= preg_replace_callback('/{%(.*?)%}/', function ($key) use ($data, $no) {
                if ($key[1] === '__no') {
                    return $no;
                }
                return $this->dataGet($data, $key[1]);
            }, $template);
            $no++;
        }
        return $render;
    }

    protected function buildTemplateDataList(&$template)
    {
        if ($matchDataList = $this->checkPatternDataList($template)) {
            if (isset($matchDataList[0]) && isset($matchDataList[1]) && isset($matchDataList[2])) {
                foreach ($matchDataList[0] as $k => $outerHtml) {
                    $inner = (isset($matchDataList[2][$k]) ? $matchDataList[2][$k] : '');
                    $outer = $outerHtml;
                    $data = (isset($matchDataList[1][$k]) ? $matchDataList[1][$k] : '');
                    if ($matchDataListSub = $this->checkPatternDataList($inner)) {
                        str_replace($inner, $this->buildTemplateDataList($inner), $inner);
                    }
                    $template = str_replace($outer, $this->buildTemplateDataListData($data, $inner), $template);

                }
            }
        }
    }

    protected function checkPatternConditions($template)
    {
        preg_match_all('/<!--%condition:(.*)%-->(.*)?<!--%end_condition:\1%-->/siU', $template, $matchDataList);
        return $matchDataList;
    }

    protected function buildTemplateConditions(&$template)
    {
        if ($matchDataList = $this->checkPatternConditions($template)) {
            if (isset($matchDataList[0]) && isset($matchDataList[1]) && isset($matchDataList[2])) {
                $conditions = $this->conditions();
                foreach ($matchDataList[0] as $k => $outerHtml) {
                    $inner = (isset($matchDataList[2][$k]) ? $matchDataList[2][$k] : '');
                    $outer = $outerHtml;
                    $data = (isset($matchDataList[1][$k]) ? $matchDataList[1][$k] : '');
                    $this->buildTemplateConditions($inner);
                    if($data && isset($conditions[$data]) && is_callable($conditions[$data])){
                        if(call_user_func($conditions[$data], $this->_data_vars)){
                            $template = str_replace($outer, $inner, $template);
                        }else{
                            $template = str_replace($outer, '', $template);
                        }
                    }

                }
            }
        }
    }

    /**
     * Build language data pada template
     */
    public function buildTemplateLang()
    {
        preg_match_all('/{lang:(.*)?}/siU', $this->_template_render, $match);
        if (isset($match[0]) && isset($match[1])) {
            $langs = array_combine($match[0], $match[1]);
            $langSource = 'yihai';
            if (isset(Yihai::$app->i18n->translations['yihai-' . $this->model->module]))
                $langSource = 'yihai-' . $this->model->module;

            foreach ($langs as $template => $lang) {
                $this->_template_render = str_replace($template, Yihai::t($langSource, $lang), $this->_template_render);
            }
        }


    }

    /**
     * Build language data
     */
    public function buildTemplateGlobalVars()
    {
        $this->_template_render = preg_replace_callback('/{(?!\%)(.*?)(?!\%)}/', function ($key) {
            return $this->dataGet($this->_data_vars['global'], $key[1],'global');
        }, $this->_template_render);


    }

    /**
     * Formatters data
     * ```php
     * [
     *      'time' => [
     *          'full' => function($value) { return date('Y-m-d',$value); }
     *      ]
     * ]
     * ```
     * @return array
     */
    public function formatters()
    {
        return Yihai::$app->reports->formatters();
    }

    /**
     * conditions list
     * - $data_vars adalah list data
     * - return harus bool
     * * ```php
     * [
     *      'user_is_system' => [
     *          'full' => function($data_vars) { return $data_vars['global']['user']['id'] === 1; }
     *      ]
     * ]
     * ```
     *
     * @return array
     */
    public function conditions()
    {
        return [];
    }

    /**
     * @param $query
     * @return array|\yihai\core\db\ActiveRecord[]|null
     */
    public function dataQuery($query)
    {
        if (!isset($this->_db_queries[$query]))
            return [];

        return $this->_db_queries[$query]->all();

    }

    /**
     * get data one dari $this->_db_queries
     * @param $query
     * @return array|\yihai\core\db\ActiveRecord|null
     */
    public function dataQueryOne($query)
    {
        if (!isset($this->_db_queries[$query]))
            return [];
        return $this->_db_queries[$query]->one();

    }

    /**
     * get all available fields
     * @return array
     */
    public function getAllAvailableFields()
    {
        $comp = Yihai::$app->reports->dataVars();
        $comp['global'] = ArrayHelper::keys_multi($comp['global']);
        $comp['global']['filter'] = array_keys($this->filterModel->attributes);
        $fields = ArrayHelper::merge($comp, $this->availableFields());
        return $fields;

    }

    /**
     * @return ActiveQuery[]
     */
    public function getDbQueries()
    {
        return $this->_db_queries;
    }

    /**
     * @return string
     */
    public function getFilterModelName()
    {
        return 'report-' . $this->model->key;
    }

    /**
     * @return FilterModel
     */
    public function getFilterModel()
    {
        return $this->_filterModel;
    }

    /**
     * gunakan @see filterHtml untuk membuat inputan
     * @throws \Exception
     */
    public function renderFilterHtml()
    {
        $form = ActiveForm::begin([]);
        $this->filterHtml($form);
        echo Html::beginTag('div', ['class' => 'btn-group']);
        echo Button::widget(['label' => Html::icon('eye'), 'encodeLabel' => false, 'options' => ['title' => Yihai::t('yihai', 'Lihat')]]);
        echo Button::widget([
            'label' => Html::icon('print'),
            'encodeLabel' => false,
            'options' => ['name' => 'type', 'formtarget' => '_blank', 'formaction' => Url::to(['export-report', 'key' => $this->model->key, '__type' => 'print']), 'title' => Yihai::t('yihai', 'Cetak')]
        ]);
        echo Button::widget([
            'label' => Html::icon('download'),
            'encodeLabel' => false,
            'options' => ['name' => 'type', 'formtarget' => '_blank', 'formaction' => Url::to(['export-report', 'key' => $this->model->key, '__type' => 'pdf']), 'title' => Yihai::t('yihai', 'Unduh ({type})', ['type'=>'PDF'])]
        ]);
        echo Html::endTag('div');
        ActiveForm::end();

    }

    /**
     * format halaman untuk mpdf
     * @return array
     */
    public function pageFormats()
    {

        return [
            '4A0' => [4767.87, 6740.79],
            '2A0' => [3370.39, 4767.87],
            'A0' => [2383.94, 3370.39],
            'A1' => [1683.78, 2383.94],
            'A2' => [1190.55, 1683.78],
            'A3' => [841.89, 1190.55],
            'A4' => [595.28, 841.89],
            'A5' => [419.53, 595.28],
            'A6' => [297.64, 419.53],
            'A7' => [209.76, 297.64],
            'A8' => [147.40, 209.76],
            'A9' => [104.88, 147.40],
            'A10' => [73.70, 104.88],
            'B0' => [2834.65, 4008.19],
            'B1' => [2004.09, 2834.65],
            'B2' => [1417.32, 2004.09],
            'B3' => [1000.63, 1417.32],
            'B4' => [708.66, 1000.63],
            'B5' => [498.90, 708.66],
            'B6' => [354.33, 498.90],
            'B7' => [249.45, 354.33],
            'B8' => [175.75, 249.45],
            'B9' => [124.72, 175.75],
            'B10' => [87.87, 124.72],
            'C0' => [2599.37, 3676.54],
            'C1' => [1836.85, 2599.37],
            'C2' => [1298.27, 1836.85],
            'C3' => [918.43, 1298.27],
            'C4' => [649.13, 918.43],
            'C5' => [459.21, 649.13],
            'C6' => [323.15, 459.21],
            'C7' => [229.61, 323.15],
            'C8' => [161.57, 229.61],
            'C9' => [113.39, 161.57],
            'C10' => [79.37, 113.39],
            'RA0' => [2437.80, 3458.27],
            'RA1' => [1729.13, 2437.80],
            'RA2' => [1218.90, 1729.13],
            'RA3' => [864.57, 1218.90],
            'RA4' => [609.45, 864.57],
            'SRA0' => [2551.18, 3628.35],
            'SRA1' => [1814.17, 2551.18],
            'SRA2' => [1275.59, 1814.17],
            'SRA3' => [907.09, 1275.59],
            'SRA4' => [637.80, 907.09],
            'LETTER' => [612.00, 792.00],
            'LEGAL' => [612.00, 1008.00],
            'LEDGER' => [1224.00, 792.00],
            'TABLOID' => [792.00, 1224.00],
            'EXECUTIVE' => [521.86, 756.00],
            'FOLIO' => [612.00, 936.00],
            'B' => [362.83, 561.26], // 'B' format paperback size 128x198mm
            'A' => [314.65, 504.57], // 'A' format paperback size 111x178mm
            'DEMY' => [382.68, 612.28], // 'Demy' format paperback size 135x216mm
            'ROYAL' => [433.70, 663.30], // 'Royal' format paperback size 153x234mm
        ];

    }

    /**
     * @return array
     */
    public function mpdf()
    {
        return [
            'default_font_size' => 0,
            'default_font' => '',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
        ];
    }
    /**
     * menambah atau mengganti mpdf options
     * @param \Mpdf\Mpdf $mpdf
     */
    public function mpdfOptions(&$mpdf)
    {
        $mpdf->SetFooter(array(
            'odd' => array(
                'L' => array(
                    'content' => '',
                    'font-size' => 10,
                    'font-style' => 'B',
                    'font-family' => 'serif',
                    'color' => '#000000'
                ),
                'C' => array(
                    'content' => '{PAGENO}/{nbpg}',
                    'font-size' => 10,
                    'font-style' => 'N',
                    'font-family' => 'serif',
                    'color' => '#636363'
                ),
                'R' => array(
                    'content' => $this->model->key,
                    'font-size' => 10,
                    'font-style' => 'N',
                    'font-family' => 'serif',
                    'color' => '#636363'
                ),
                'line' => 1,
            ),
        ));
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->_desc;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

}