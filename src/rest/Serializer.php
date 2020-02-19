<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\rest;


class Serializer extends \yii\rest\Serializer
{
    protected function getRequestedFields()
    {
            $fields = $this->request->post($this->fieldsParam);
            $expand = $this->request->post($this->expandParam);
            if(empty($fields))
                $fields = $this->request->get($this->fieldsParam);
            if(empty($expand))
                $expand = $this->request->get($this->expandParam);
            return [
                is_string($fields) ? preg_split('/\s*,\s*/', $fields, -1, PREG_SPLIT_NO_EMPTY) : [],
                is_string($expand) ? preg_split('/\s*,\s*/', $expand, -1, PREG_SPLIT_NO_EMPTY) : [],
            ];

    }
}