<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


interface CrudInterface
{

    /**
     * class model
     * @return string|\yihai\core\db\ActiveRecord
     */
    public function _modelClass();

    /**
     * update model options
     * @param \yihai\core\base\ModelOptions $options
     * @return void
     */
    public function _modelOptions(&$options);

}