<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web\response;


use Mpdf\Mpdf;
use Yihai;
use yii\base\Component;
use yii\web\Response;
use yii\web\ResponseFormatterInterface;

/**
 * Class MpdfFormatter
 * @package yihai\core\web\response
 * @property Mpdf $mpdf
 */
class MpdfFormatter extends Component implements ResponseFormatterInterface
{

    /**
     * @var string the Content-Type header for the response
     */
    public $contentType = 'application/apdf';

    /**
     * @var array
     */
    public $mpdfConfig = [];

    /**
     * @var string 'L'=Landscape or 'P'=Portrait
     */
    public $orientation = 'P';

    public $format = 'A4';

    public $fileName = 'file.pdf';
    /**
     * F = file
     * D = download
     * S = string
     * I = inline
     * @see \Mpdf\Output\Destination
     * @var string output mode default 'I'
     */
    public $dest = 'I';
    /**
     * true, write html
     * false, no write mode
     * @var bool
     */
    public $writeMode = true;

    /**
     * @var null|callable
     */
    public $mpdf = null;

    /**
     * Formats the specified response.
     * @param Response $response the response to be formatted.
     * @throws \Mpdf\MpdfException
     */
    public function format($response)
    {
        $response->getHeaders()->set('Content-Type', $this->contentType);
        $mpdf = $this->mpdf();
        if(is_callable($this->mpdf)){
            call_user_func($this->mpdf, $mpdf);
        }
        if($this->writeMode)
            $mpdf->WriteHTML($response->data);
        $mpdf->Output($this->fileName, $this->dest);
    }

    protected function mpdf()
    {
        $mpdf = new Mpdf(array_merge([
            'tempDir' => Yihai::getAlias('@runtime/mpdf'),
            'orientation' => $this->orientation,
            'format' => $this->format

        ], $this->mpdfConfig));
        return $mpdf;
    }
}