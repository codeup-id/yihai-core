<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\actions;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use Yihai;
use yihai\core\base\DynamicModel;
use yihai\core\theming\Alert;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class CrudExportAction extends CrudAction
{

    protected $type = self::TYPE_EXPORT;

    private $attributes = [];

    public function init()
    {
        $this->exception_limit();
        parent::init();
    }

    protected function buildAttributes()
    {
        if (empty($this->modelOptions->exportAttributes))
            $this->modelOptions->exportAttributes = $this->model->attributes();
        foreach ($this->modelOptions->exportAttributes as $exportAttribute) {

            preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $exportAttribute, $matches);

            $this->attributes[$exportAttribute] = [

                'attribute' => $matches[1],
                'format' => isset($matches[3]) ? $matches[3] : 'text',
                'label' => isset($matches[5]) ? $matches[5] : $matches[1],
            ];
            $this->attributes[$exportAttribute]['label'] = $this->model->getAttributeLabel($this->attributes[$exportAttribute]['label']);
        }
    }

    protected function runExport($attributes, $format)
    {
        $dataProvider = new ActiveDataProvider();
        $this->model->initDataProvider($dataProvider);
        $query = $dataProvider->query->all();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Yihai App')
            ->setLastModifiedBy('Yihai System')
            ->setTitle('Yihai Export')
            ->setSubject('Yihai Export')
            ->setDescription('Yihai Export for ' . $this->modelClass)
            ->setKeywords('yihai export excel')
            ->setCategory('Yihai Export');

        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet()->setTitle('MAIN EXPORT')->setCodeName('MAIN');
        $attributes_build = [];
        foreach ($this->attributes as $key => $attribute) {
            if (in_array($key, $attributes)) {
                $attributes_build[$key] = $attribute;
            }
        }
        $cellSerial = $sheet->getCellByColumnAndRow(1, 1)->setValue('#');
        $cellSerial->getStyle()->getFont()->setBold('TRUE')->setSize(13);
        $cellSerial->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $cellSerial->getStyle()->getAlignment()->setHorizontal('center')->setVertical('center');
        $cellColumn = 2;
        foreach ($attributes_build as $key => $attribute) {
            $cellGroup = $sheet->getCellByColumnAndRow($cellColumn, 1)->setValue($attribute['label']);
            $cellGroup->getStyle()->getFont()->setBold('TRUE');
            $cellGroup->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $cellGroup->getStyle()->getAlignment()->setHorizontal('center')->setVertical('center');
            $cellColumn++;
        }

        $rowIndex = 2;
        Yihai::$app->formatter->nullDisplay = '-';
        $i = 1;
        foreach ($query as $data) {
            /** @var \yihai\core\db\ActiveRecord $data */
            $colIndex = 2;
            $cell = $sheet->getCellByColumnAndRow(1, $rowIndex)->setValue($i);
            $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            foreach ($attributes_build as $key => $attribute) {
                $dataValue = ArrayHelper::getValue($data, $attribute['attribute']);
                $value = Yihai::$app->formatter->format($dataValue, $attribute['format']);
                if ($value == '')
                    $value = '-';
                $cell = $sheet->getCellByColumnAndRow($colIndex, $rowIndex)->setValue($value);
                $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $cell->getStyle()->getAlignment()->setWrapText(TRUE);
                $colIndex++;
            }
            $i++;
            $rowIndex++;
        }
        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $maxWidth = 70;
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheet->calculateColumnWidths();
            foreach ($sheet->getColumnDimensions() as $colDim) {
                if (!$colDim->getAutoSize()) {
                    continue;
                }
                $colWidth = $colDim->getWidth();
                if ($colWidth > $maxWidth) {
                    $colDim->setAutoSize(false);
                    $colDim->setWidth($maxWidth);
                }
            }
        }

        $fileName = 'Export-' . trim(str_replace(['\\', '/'], '-', $this->modelClass), '-');
        $fileName .= ' (' . date('Y-m-d H-i-s') . ')';
        if ($format === 'html') {
            $writer = IOFactory::createWriter($spreadsheet, 'Html');
            $fileName .= '.html';
            header('Content-Type: text/html');
        } elseif ($format === 'pdf') {
            $writer = new Mpdf($spreadsheet);
            $writer->setTempDir(Yihai::getAlias('@runtime/mpdf'));
            $fileName .= '.pdf';
            header('Content-Type: application/pdf');
        } elseif ($format === 'csv') {
            $writer = IOFactory::createWriter($spreadsheet, 'Csv');
            $fileName .= '.csv';
            header('Content-Type: application/csv');
        } elseif ($format === 'text') {
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter("\t");
            $fileName .= '.txt';
            header('Content-Type: text/plain');
        } elseif ($format === 'xls') {
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $fileName .= '.xls';
            header('Content-Type: application/vnd.ms-excel');
        } else {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $fileName .= '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

    public function run()
    {
        $this->buildAttributes();
        $exportForm = new DynamicModel(['attributes', 'format']);
        $exportForm->addRule('attributes', 'required');
        $exportForm->addRule('format', 'required');
        if ($exportForm->load(Yihai::$app->request->post())) {
            if (!$exportForm->attributes) {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger',Yihai::t('yihai', 'Atribut ekspor tidak boleh kosong'));
                return $this->controller->redirect($this->modelOptions->getActionUrlTo('export'));
            }
            return $this->runExport($exportForm->attributes, $exportForm->format);
        }
        $this->addParams('exportForm', $exportForm);
        $this->addParams('exportAttributes', $this->attributes);
        return parent::run();
    }
}