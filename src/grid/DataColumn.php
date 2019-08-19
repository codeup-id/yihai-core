<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\grid;


class DataColumn extends \yii\grid\DataColumn
{
    public function init()
    {
        parent::init();
        $this->filterInputOptions = array_merge(['class' => 'form-control', 'id' => null], $this->filterInputOptions);
    }

    protected function renderFilterCellContent()
    {
        if(is_callable($this->filter))
            $this->filter = call_user_func($this->filter);
        return parent::renderFilterCellContent();
    }
}