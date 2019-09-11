<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web\response;


use Mpdf\Mpdf;
use Mpdf\Output\Destination;
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
    public $contentType = 'application/pdf';

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
    public $dest = 'S';
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
        if($this->dest === Destination::STRING_RETURN){
            $content = $mpdf->Output($this->fileName, $this->dest);
            if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) || empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
                // don't use length if server using compression
                header('Content-Length: ' . strlen($content));
            }
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            $response->content = $content;
        }else{
            $mpdf->Output($this->fileName, $this->dest);
        }
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