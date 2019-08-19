<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


use Yihai;
use yihai\core\db\ActiveRecord;
use yii\base\InvalidConfigException;

class ActiveForm extends \yii\widgets\ActiveForm
{
    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     * @see fieldConfig
     */

//    public $fieldClass = 'yii\bootstrap\ActiveField';

    public $fieldClass = 'yihai\core\theming\ActiveField';
    /**
     * @var array HTML attributes for the form tag. Default is `[]`.
     */
    public $options = [];
    /**
     * @var string the form layout. Either 'default', 'horizontal' or 'inline'.
     * By choosing a layout, an appropriate default field configuration is applied. This will
     * render the form fields with slightly different markup for each layout. You can
     * override these defaults through [[fieldConfig]].
     * @see \yii\bootstrap\ActiveField for details on Bootstrap 3 field configuration
     */
    public $layout = 'default';


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (!in_array($this->layout, ['default', 'horizontal', 'inline'])) {
            throw new InvalidConfigException('Invalid layout type: ' . $this->layout);
        }

        if ($this->layout !== 'default') {
            Html::addCssClass($this->options, 'form-' . $this->layout);
        }
        parent::init();
    }

    /**
     * {@inheritdoc}
     * @return ActiveField the created ActiveField object
     */
    public function field($model, $attribute, $options = [])
    {
        if($model instanceof ActiveRecord){
            if($model->queryDefaultValue($attribute))
                $options['options']['style']='display:none';
        }
        return parent::field($model, $attribute, $options);
    }
}