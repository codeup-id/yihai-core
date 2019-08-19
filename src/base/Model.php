<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


class Model extends \yii\base\Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';


    protected $_formName;

    public function formName()
    {
        return $this->_formName ?: parent::formName();
    }

    public function setFormName($name)
    {
        $this->_formName = $name;
    }
}