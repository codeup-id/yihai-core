<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\grid;


use yihai\core\theming\Html;
use yii\helpers\Url;

class CheckboxColumn extends \yii\grid\CheckboxColumn
{

    public $headerOptions = ['style' => 'width:1px;text-align:center'];
    public $contentOptions = ['style' => 'width:1px;text-align:center'];
    public $checkboxOptions = ['class' => 'grid-col-select'];
    /**
     * @var \yihai\core\base\ModelOptions
     */
    public $modelOptions;
    public function init()
    {
        parent::init();
        if(!$this->footer){
            $this->footer = Html::a(Html::icon('trash',['class'=>'text-danger']), Url::to([$this->modelOptions->getActionUrl('delete')]), [
                'class' => 'btn-delete-selected',
                'style' => 'display:none',
                'data-multiple' => true,
                'data-target' => '#yihai-crud-basemodal-delete',
                'data-toggle' => 'modal'
            ]);
        }
    }
}