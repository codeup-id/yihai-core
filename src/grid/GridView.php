<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\grid;


use Yihai;
use yihai\core\helpers\Url;
use yihai\core\theming\Html;

class GridView extends \dosamigos\grid\GridView
{

    public $summaryOptions = [
        'tag' => 'span'
    ];

    public function init()
    {
        parent::init();
        if($this->dataProvider->getPagination()) {
            $sessId = 'pager-' . $this->id;
            if ($sess_pager = Yihai::$app->request->get($sessId)) {
                Yihai::$app->session->set($sessId, $sess_pager);
            }
            if ($sess_pager = Yihai::$app->session->get($sessId))
                $this->dataProvider->getPagination()->pageSize = $sess_pager;
        }
    }


    public function renderSummary()
    {
        if($this->dataProvider->getPagination()) {
            $size = [
                10 => 10,
                20 => 20,
                50 => 50,
                100 => 100,
                -1 => Yihai::t('yihai', 'Semua')
            ];
            $default_pageSize = $this->dataProvider->getPagination()->pageSize;
            if (!isset($size[$default_pageSize])) {
                if ($default_pageSize <= 0)
                    $default_pageSize = -1;
                else
                    $size[$default_pageSize] = $default_pageSize;
            }
            $active = $default_pageSize;
            $sessId = 'pager-' . $this->id;
            $parent = parent::renderSummary();
            return Html::tag('span',
                    Html::beginForm(Url::current(), 'get', ['data-pjax' => true, 'style' => 'width:250px']) .
                    Html::dropDownList($sessId, $active, $size, ['onchange' => 'if($){$(this).closest(\'form\').submit();}else{this.form.submit()}', 'class' => 'form-control']) .
                    Html::endForm()) .
                $parent;
        }
        return parent::renderSummary();

    }

    public $dataColumnClass = 'yihai\core\grid\DataColumn';
}