<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\actions;


use Mpdf\Output\Destination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use Yihai;
use yihai\core\grid\GridView;
use yihai\core\modules\system\ModuleSetting;
use yihai\core\theming\LinkPager;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yihai\core\web\Response;

class CrudIndexAction extends CrudAction
{
    protected $type = self::TYPE_INDEX;

    /**
     * @var array|ActiveDataProvider
     */
    public $dataProvider;

    public $gridColumns;
    /**
     * @var array|GridView
     */
    public $gridConfig = [];

    private $_grid_export;

    public function init()
    {
        $this->exception_limit();
        if ($this->viewFile)
            $this->modelOptions->viewFileIndex = $this->viewFile;
        if (($_grid_export = Yihai::$app->request->get('_grid_export')) && in_array($_grid_export, ['print', 'pdf', 'xlsx', 'csv', 'html'])) {
            $this->_grid_export = $_grid_export;
            $this->addParams('_grid_export', $this->_grid_export);
            $this->controller->layout = '@yihai/views/_layouts/blank-content';
            $this->baseViewFile = $this->modelOptions->viewFileGridExport;
            $this->modelOptions->gridViewActionColumn = false;
            $this->modelOptions->gridViewCheckboxColumn = false;

        }
        parent::init();
    }

    public function run()
    {
        if ($this->_grid_export) {
            /** @var \yihai\core\modules\system\ModuleSetting $systemSetting */
            $systemSetting = \yihai\core\modules\system\Module::loadSettings();
            if ($this->_grid_export === 'print' || $this->_grid_export === 'pdf') {
                Yihai::$app->response->format = Response::FORMAT_PDF;
                Yihai::$app->response->formatters['pdf'] = [
                    'class' => 'yihai\core\web\response\MpdfFormatter',
                    'orientation' => $this->modelOptions->gridPdfOrientation,
                    'fileName' => "Export " . $this->modelOptions->baseTitle . '.pdf',
                    'dest' => $this->_grid_export === 'print' ? Destination::INLINE : Destination::DOWNLOAD,
                    'mpdfConfig' => [
                        'showWatermarkImage' => true,
                    ],
                    'format' => $this->modelOptions->gridPdfSize,
                    'mpdf' => function(\Mpdf\Mpdf $mpdf) use($systemSetting){
                        $mpdf->SetTitle($this->modelOptions->baseTitle . ' Data');
                        $mpdf->SetAuthor(Yihai::$app->user->identity->model->username . ' (Yihai App)');
                        $mpdf->SetWatermarkImage($systemSetting->gridExportWatermark_image->fullpath, 0.1, 40, 'F');
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
                                    'content' => $this->modelOptions->baseTitle . ' Data',
                                    'font-size' => 10,
                                    'font-style' => 'N',
                                    'font-family' => 'serif',
                                    'color' => '#636363'
                                ),
                                'line' => 1,
                            ),
                        ));
                        if(is_callable($this->modelOptions->gridExportMpdf)){
                            call_user_func($this->modelOptions->gridExportMpdf, $mpdf);
                        }
                    }
                ];
                return parent::run();
            } elseif ($this->_grid_export === 'xlsx') {
                $spreadsheet = $this->spreadsheetFromHtml($systemSetting);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Export ' . $this->modelOptions->baseTitle . '.xlsx"');
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
                exit;
            } elseif ($this->_grid_export === 'csv') {
                $spreadsheet = $this->spreadsheetFromHtml($systemSetting);
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment;filename="Export ' . $this->modelOptions->baseTitle . '.csv"');
                $writer = IOFactory::createWriter($spreadsheet, 'Csv');
                $writer->save('php://output');
                exit;
            }
        }
        return parent::run();
    }

    /**
     * @param ModuleSetting $systemSetting
     * @return Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \yii\base\Exception
     */
    private function spreadsheetFromHtml($systemSetting)
    {
        $html = parent::run();
        $fileTmp = Yihai::writeRuntimeTmpFile('export-' . Yihai::$app->security->generateRandomString(8), $html);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Yihai App')
            ->setLastModifiedBy('Yihai System')
            ->setTitle('Yihai Export')
            ->setSubject('Yihai Export')
            ->setDescription('Yihai Export for ' . $this->modelClass)
            ->setKeywords('yihai export excel')
            ->setCategory('Yihai Export GRID');

        $excelHTMLReader = IOFactory::createReader('Html');
        $excelHTMLReader->loadIntoExisting($fileTmp, $spreadsheet);
        $spreadsheet->getActiveSheet()->setTitle('MAIN EXPORT');
        unlink($fileTmp);
        $activeSheet = $spreadsheet->getActiveSheet();
        foreach (range('A', $activeSheet->getHighestColumn()) as $columnID) {
            $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $drawing = new HeaderFooterDrawing();
        $drawing->setName('PhpSpreadsheet logo');
        $drawing->setPath($systemSetting->gridExportWatermark_image->fullpath);
        $drawing->setHeight(36);
        $spreadsheet->getActiveSheet()
            ->getHeaderFooter()
            ->addImage($drawing, HeaderFooter::IMAGE_HEADER_LEFT);

        return $spreadsheet;
    }

    protected function init_gridView()
    {
        $gridColumns = [];
        if ($this->modelOptions->getGridViewSerialColumn()) {
            $gridColumns[] = $this->modelOptions->getGridViewSerialColumn();
        }
        if ($this->modelOptions->getGridViewCheckboxColumn() && $this->modelOptions->userCanAction('delete')) {
            $gridColumns[] = $this->modelOptions->getGridViewCheckboxColumn();
        }
        if ($this->modelOptions->getGridViewActionColumn()) {
            $gridColumns[] = $this->modelOptions->getGridViewActionColumn();
        }
        if ($this->gridColumns) {
            $this->modelOptions->gridColumnData = $this->gridColumns;
        }
        $gridColumns = ArrayHelper::merge($gridColumns, $this->modelOptions->gridColumnData);

        /** @var ActiveDataProvider $dataProvider */
        if ($this->dataProvider)
            $this->modelOptions->gridDataProvider = $this->dataProvider;
        if (is_array($this->modelOptions->gridDataProvider)) {
            $dataProvider = new ActiveDataProvider(ArrayHelper::merge([
                'pagination' => [
                    'pageSize' => 10
                ],
            ], $this->modelOptions->gridDataProvider));
        } else {
            $dataProvider = $this->modelOptions->gridDataProvider;
        }
        if ($this->model->hasMethod('initDataProvider')) {
            $this->model->initDataProvider($dataProvider);
        } else {
            $modelClass = $this->modelClass;
            $dataProvider->query = $modelClass::find();
        }
        if ($this->model->hasMethod('getFilterModel'))
            $filterModel = $this->model->getFilterModel();
        else
            $filterModel = null;

        $this->modelOptions->gridViewConfig = ArrayHelper::merge([
            'class' => GridView::class,
            'showFooter' => true,
            'pager' => [
                'class' => LinkPager::class,
                'lastPageLabel' => '&raquo;&raquo;',
                'firstPageLabel' => '&laquo;&laquo;'
            ],
            'behaviors' => [
                [
                    'class' => '\dosamigos\grid\behaviors\LoadingBehavior',
                    'type' => 'bars'
                ]
            ],
            'columns' => $gridColumns,
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel
        ], $this->modelOptions->gridViewConfig);
    }

    protected function beforeRun()
    {
        if ($this->gridConfig)
            $this->modelOptions->gridViewConfig = $this->gridConfig;
        $this->init_gridView();
        return parent::beforeRun();
    }

}