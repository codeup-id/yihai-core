<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\web;


class Response extends \yii\web\Response
{
    const FORMAT_PDF = 'pdf';

    /**
     * @inheritDoc
     */
    protected function defaultFormatters()
    {
        return array_merge(parent::defaultFormatters(), [
            self::FORMAT_PDF => [
                'class' => 'yihai\core\web\response\MpdfFormatter'
            ]
        ]);
    }

}