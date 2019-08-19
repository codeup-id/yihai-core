<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system;


use Yihai;
use yihai\core\models\SysUploadedFiles;

class ModuleSetting extends \yihai\core\base\ModuleSetting
{
    public $defaultEmailDomain = '@codeup.id';
    /**
     * @var SysUploadedFiles
     */
    public $gridExportWatermark_image = 'image';
    public $gridExportPrint_header;
    public $gridExportPrint_Watermark = 1;
    public $gridExportPdf_Watermark = 1;

    public $reportWatermark = 1;
    public $reportWatermarkImage = 'image';
    public $reportHeader;


    public function attributeLabels()
    {
        return [
        ];
    }

    public function fieldTypes()
    {
        return [
            'gridExportPrint_Watermark' => self::FIELD_YESNO,
            'gridExportWatermark_image' => self::FIELD_IMAGE,
            'gridExportPrint_header' => self::FIELD_HTML,
            'gridExportPdf_Watermark' => self::FIELD_YESNO,
            'gridExportPdf_Watermark_image' => self::FIELD_IMAGE,


            'reportWatermark' => self::FIELD_YESNO,
            'reportWatermarkImage' => self::FIELD_IMAGE,
            'reportHeader' => self::FIELD_HTML,
        ];
    }

    public function attributeHints()
    {
        return [
            'gridExportWatermark_image' => Yihai::t('yihai', 'Image watermark for grid data print/pdf.'),
            'gridExportPrint_header' => Yihai::t('yihai', 'Header for grid data print/pdf.'),
            'gridExportPrint_Watermark' => Yihai::t('yihai', 'Use watermark for grid data print.'),
            'gridExportPdf_Watermark' => Yihai::t('yihai', 'Use watermark for grid data PDF.'),
            'reportWatermark' => Yihai::t('yihai', 'Use watermark for report PDF.'),
            'reportWatermarkImage' => Yihai::t('yihai', 'Image watermark for report.'),
            'reportHeader' => Yihai::t('yihai', 'Header for report.'),
        ];
    }

}