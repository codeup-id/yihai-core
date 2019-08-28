<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\actions;


use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Yihai;
use yihai\core\base\DynamicModel;
use yihai\core\db\ActiveRecord;
use yihai\core\theming\Alert;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class CrudImportAction extends CrudAction
{
    protected $type = self::TYPE_IMPORT;


    private $_import_file;

    protected $_tmp_dir = '@yihai/runtime/imports/';
    public $importAttributes = [];

    public function init()
    {
        $this->exception_limit();
        parent::init();
        if ($this->importAttributes)
            $this->modelOptions->importAttributes = $this->importAttributes;
        $this->_import_file = trim(str_replace(['\\', '/'], '-', $this->modelOptions->getActionUrl('import')), '-');
        $this->_tmp_dir = Yihai::getAlias($this->_tmp_dir);
    }

    protected function downloadTemplate()
    {
        if (is_file($this->controller->getViewPath() . '/import.xlsx')) {
            $fileName = trim(str_replace(['\\', '/'], '-', $this->modelClass), '-');
            Yihai::$app->response->sendFile($this->controller->getViewPath() . '/import.xlsx', 'import ' . $fileName . '.xlsx');
//            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//            $fileName = trim(str_replace(['\\', '/'], '-', $this->modelClass), '-');
//            header('Content-Disposition: attachment; filename="' . 'import ' . $fileName . '.xlsx' . '"');
            exit;
        }
        if (empty($this->modelOptions->importAttributes)) {
            Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Yihai::t('yihai', 'Atribut "importAttributes" tidak disetel'));
            return $this->controller->redirect($this->modelOptions->getActionUrlTo('index'));
        }
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Yihai App')
            ->setLastModifiedBy('Yihai System')
            ->setTitle('Yihai Import')
            ->setSubject('Yihai Import')
            ->setDescription('Yihai Import for ' . $this->modelClass)
            ->setKeywords('yihai import excel')
            ->setCategory('Yihai Import');

        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet()->setTitle('MAIN IMPORT')->setCodeName('MAIN');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(16);
        $i = 1;
        foreach ($this->modelOptions->getImportAttributes() as $column) {
            if ($this->model->hasProperty($column['attribute'])) {
                $label = $this->model->getAttributeLabel($column['label']);
                $col = $sheet->getCellByColumnAndRow($i, 1);
                $col->setValue($label);
                $col->getStyle()->getAlignment()->setHorizontal('center')->setVertical('center');
                $col->getStyle()->getFont()->setBold(true);
                $col->getStyle()->getFont()->setSize(20);
                $colIndex = Coordinate::stringFromColumnIndex($i) . 1;
                $sheet->getComment($colIndex)->getText()->createTextRun($column['data']);

                $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
                for ($j = 1; $j <= $this->modelOptions->importMax + 1; $j++) {
                    $colNext = $sheet->getCellByColumnAndRow($i, $j);
                    $colNext->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $colNext->getStyle()->getAlignment()->setWrapText(TRUE);
                    $colNext->getStyle()->getAlignment()->setVertical('top');
                    if ($j > 1) {
                        $colNext->getStyle()->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                    }

                }
                $i++;
            }
        }
        if ($this->modelOptions->importInfo) {
            $sheet->mergeCellsByColumnAndRow($i + 3, 3, $i + 8, 3);
            $colInfo = $sheet->getCellByColumnAndRow($i + 3, 3)->setValue('INFO');
            $colInfo->getStyle()->getFont()->setBold('TRUE')->setSize(30);
            $colInfo->getStyle()->getAlignment()->setHorizontal('center')->setVertical('center');

            $sheet->mergeCellsByColumnAndRow($i + 3, 4, $i + 8, (4 + count($this->modelOptions->importInfo) * 3));
            $colInfotext = $sheet->getCellByColumnAndRow($i + 3, 4);
            $colInfotext->getStyle()->getAlignment()->setVertical('top')->setWrapText(true);
            $colInfotext->setValue(implode("\n", $this->modelOptions->importInfo));

        }
        if ($this->modelOptions->importRefs) {
            foreach ($this->modelOptions->importRefs as $key => $ref) {
                $sheetRef = $spreadsheet->createSheet()->setTitle($key);
                /** @var ActiveRecord $refModelClass */
                $refModelClass = $ref['model'];
                /** @var ActiveRecord $refModel */
                $refModel = new $ref['model']();
                $i = 1;
                foreach ($ref['attributes'] as $attribute) {
                    $col = $sheetRef->getCellByColumnAndRow($i, 1);
                    $col->setValue($refModel->getAttributeLabel($attribute));
                    $col->getStyle()->getAlignment()->setHorizontal('center')->setVertical('center');
                    $col->getStyle()->getFont()->setBold(true);
                    $col->getStyle()->getFont()->setSize(20);
                    $col->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $col->getStyle()->getAlignment()->setVertical('top');
                    $i++;
                }
                $row = 2;
                foreach ($refModelClass::find()->all() as $data) {
                    $j = 1;
                    foreach ($ref['attributes'] as $attribute) {
                        $col = $sheetRef->getCellByColumnAndRow($j, $row);
                        $col->setValue(ArrayHelper::getValue($data, $attribute));
                        $col->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                        $col->getStyle()->getAlignment()->setWrapText(TRUE);
                        $col->getStyle()->getAlignment()->setVertical('top');
                        $j++;
                    }
                    $row++;
                }
                $cellIterator = $sheetRef->getRowIterator()->current()->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);
                foreach ($cellIterator as $cell) {
                    $sheetRef->getColumnDimension($cell->getColumn())->setAutoSize(true);
                }
                $sheetRef->getProtection()->setPassword('yihai-import')->setSheet(true)->setInsertRows(true)->setInsertColumns(true);

            }
        }
        $spreadsheet->setActiveSheetIndexByName('MAIN IMPORT');

        $sheet->getProtection()->setPassword('yihai-import')->setSheet(true)->setInsertRows(true)->setInsertColumns(true);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $fileName = trim(str_replace(['\\', '/'], '-', $this->modelClass), '-');
        header('Content-Disposition: attachment; filename="' . 'import ' . $fileName . '.xlsx' . '"');
        $writer->save('php://output');
        exit;

    }

    /**
     * @param Worksheet $sheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function addDropDown($sheet)
    {

        $objValidation2 = $sheet->getCell('C3')->getDataValidation();
        $objValidation2->setType(DataValidation::TYPE_LIST);
        $objValidation2->setErrorStyle(DataValidation::STYLE_STOP);
        $objValidation2->setAllowBlank(false);
        $objValidation2->setShowErrorMessage(true);
        $objValidation2->setShowInputMessage(true);
        $objValidation2->setShowDropDown(true);
        $objValidation2->setPromptTitle('Pick from list');
        $objValidation2->setPrompt('Please pick a value from the drop-down list.');
        $objValidation2->setErrorTitle('Input error');
        $objValidation2->setError('Value is not in list');
        $objValidation2->setFormula1('"male,female"');
    }

    protected function runImport()
    {
        $spreadsheet = IOFactory::load($this->_tmp_dir . $this->_import_file);
        $spreadsheet->setActiveSheetIndexByName('MAIN IMPORT');
        $sheet = $spreadsheet->getActiveSheet();
        $attributes = [];

        $highestRow = $sheet->getHighestDataRow();
        $highestCol = $sheet->getHighestColumn();
        $ar = $sheet->rangeToArray("A1:$highestCol$highestRow", null, true, false, false);
        foreach ($ar as $i => $a) {
            if (empty($a))
                unset($ar[$i]);
        }
        foreach (range('A', $sheet->getHighestColumn()) as $column_key) {
            $attribute = $sheet->getComment($column_key . '1')->getText()->getPlainText();
//            if ($this->model->hasProperty($attribute)) {
            if ($attribute)
                $attributes[$column_key] = $attribute;
//            }
        }
        /** @var ActiveRecord[] $tmp_model */
        $tmp_model = [];
        for ($i = 2; $i < $sheet->getHighestRow(); $i++) {
            $isData = false;
            foreach ($attributes as $column_key => $attribute) {
                if ($sheet->getCell($column_key . $i)->getValue()) {
                    $isData = true;
                    continue;
                }
            }
            if (!$isData) continue;

            /** @var ActiveRecord $model */
            $model = new $this->modelClass();
            $model->loadDefaultValues();
            $model->addScenario($this->formType, $this->scenarioAttributes);
            // set scenario
            $model->scenario = $this->formType;
            $array_attribute = [];
            foreach ($attributes as $col => $attribute) {
                $value = $sheet->getCell($col . $i)->getValue();
                $keys = explode('.', $attribute);
                ArrayHelper::setValue($array_attribute, $keys, $value);
            }
            foreach ($array_attribute as $key => $value) {
                $model->{$key} = $value;
            }

            $tmp_model[$i] = $model;
        }
        $modelImportForm = new DynamicModel(['import']);
        $modelImportForm->addRule('import', 'safe');
        $modelImportForm->setFormName('ImportModel');
        if ($modelImportForm->load(Yihai::$app->request->post())) {
            foreach ($modelImportForm->import as $index => $val) {
                if (isset($tmp_model[$index]) && $val == 1) {
                    $tmp_model[$index]->save();
                }
            }
            if (file_exists($this->_tmp_dir . $this->_import_file)) {
                FileHelper::unlink($this->_tmp_dir . $this->_import_file);
            }
            Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Yihai::t('yihai', 'Impor selesai.'), true);
            return $this->controller->redirect($this->modelOptions->getActionUrlTo('index'));

        }
        $this->addParams('importCheck', $modelImportForm);
        $this->addParams('modelImport', $tmp_model);
        return parent::run();
    }

    public function run()
    {
        $qParams = Yihai::$app->request->getQueryParams();

        $this->addParams('custom', false);
        if (isset($qParams['custom'])) {
            if (isset($this->modelOptions->importCustom[$qParams['custom']])) {
                $custom = $this->modelOptions->importCustom[$qParams['custom']];
                $this->modelClass = $custom['model'];
                $this->model = new $custom['model']();
                $this->modelOptions->importAttributes = $custom['attributes'];
                $this->addParams('custom', $qParams['custom']);
            } else {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Yihai::t('yihai', 'Impor kustom tidak ditemukan.'));
                return $this->controller->redirect($this->modelOptions->getActionUrlTo('index'));
            }
        } else {
            if (empty($this->modelOptions->getImportAttributes())) {
                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Yihai::t('yihai', 'Atribut "importAttributes" tidak disetel'));
                return $this->controller->redirect($this->modelOptions->getActionUrlTo('index'));
            }
        }
        if (isset($qParams['downloadtemplate'])) {
            return $this->downloadTemplate();
        }
        if (isset($qParams['cancel'])) {
            if (file_exists($this->_tmp_dir . $this->_import_file)) {
                FileHelper::unlink($this->_tmp_dir . $this->_import_file);
            }
            return $this->controller->redirect($this->modelOptions->getActionUrlTo('import'));
        }

        if (file_exists($this->_tmp_dir . $this->_import_file)) {
            return $this->runImport();
        }
        $modelImportForm = new DynamicModel(['file']);
        $modelImportForm->setFormName('ImportModel');
        $modelImportForm->addRule('file', 'file', ['skipOnEmpty' => false, 'extensions' => ['xls', 'xlsx']]);
        $this->addParams('modelImportForm', $modelImportForm);

        if ($modelImportForm->load(Yihai::$app->request->post())) {
            if (!is_dir($this->_tmp_dir)) {
                FileHelper::createDirectory($this->_tmp_dir);
            }
            $modelImportForm->file = UploadedFile::getInstance($modelImportForm, 'file');
            $runtime_path = $this->_tmp_dir . $this->_import_file;
            $modelImportForm->file->saveAs(Yihai::getAlias($runtime_path));
            return $this->controller->redirect($this->modelOptions->getActionUrlTo('import'));
        }
        return parent::run();
    }
}