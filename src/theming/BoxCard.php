<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\theming;


use yihai\core\helpers\ArrayHelper;
use Yii;
use yii\base\Widget;

class BoxCard extends BaseWidget
{
    /**
     * @var string type atau style box. ex: primary|success|danger|warning|info ....etc
     */
    public $type = 'default';

    /**
     * @var
     */
    public $title;

    /**
     * @var bool
     */
    public $header = true;
    /**
     * @var
     */
    public $headerOptions;

    /**
     * @var array
     */
    public $headerTools = [];
    /**
     * @var bool
     */
    public $headerBorder = true;

    /**
     * @var string
     */
    public $headerBorderClass = 'with-border';
    /**
     * @var
     */
    public $headerToolsOptions;

    /**
     * @var array
     */
    public $headerToolsBtnOptions = [];

    public $titleOptions = [
        'tag' => 'h4',
        'class' => 'box-title'
    ];
    /**
     * @var
     */
    public $bodyOptions;

    /**
     * @var bool
     */
    public $footer = false;
    /**
     * @var
     */
    public $footerContent;
    /**
     * @var
     */
    public $footerOptions;
    /**
     * @var array
     */
    public $tools_order = ['collapse', 'remove'];

    public $tools_btn_refresh = [
        'class' => 'btn btn-box-tool',
        'data-widget' => 'refresh'
    ];
    public $tools_btn_collapse = [
        'class' => 'btn btn-box-tool',
        'data-widget' => 'collapse'
    ];
    public $tools_btn_maximize = [
        'class' => 'btn btn-box-tool',
        'data-widget' => 'maximize'
    ];
    public $tools_btn_remove = [
        'class' => 'btn btn-box-tool',
        'data-widget' => 'remove'
    ];
    public $tools_btn_disabled = ['refresh', 'maximize'];
    /**
     * @var array
     */
    public $tools_btn_custom = [

    ];

    /**
     * @var bool
     */
    public $loading = false;
    /**
     * @var bool
     */
    public $isCollapsed = false;
    /**
     * @var string
     */
    public $collapsedClass = 'collapsed-box';
    /**
     * @var bool
     */
    public $isOutline = true;

    public $outlineClass = 'box-outline';
    /** @var string konten setelah body */
    public $afterBody;

    // --------------------------------------------------------------------

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        $boxClass = [
            ArrayHelper::remove($this->options, 'class', 'box'),
            $this->typeClass()
        ];
        if ($this->isOutline)
            $boxClass[] = $this->outlineClass;
        if ($this->isCollapsed)
            $boxClass[] = $this->collapsedClass;
        Html::addCssClass($this->options, $boxClass);
        echo Html::beginTag('div', $this->options);
        if ($this->header) {
            $headerClass = [ArrayHelper::remove($this->headerOptions, 'class', 'box-header')];
            if ($this->headerBorder)
                $headerClass[] = $this->headerBorderClass;
            Html::addCssClass($this->headerOptions, $headerClass);
            echo Html::beginTag('div', $this->headerOptions);
            echo Html::tag(ArrayHelper::remove($this->titleOptions, 'tag', 'h3'), $this->title, $this->titleOptions);
            $this->_initHeaderTools();
            echo Html::endTag('div');
        }
        $bodyClass = [ArrayHelper::remove($this->bodyOptions, 'class', 'box-body')];
        Html::addCssClass($this->bodyOptions, $bodyClass);
        echo Html::beginTag('div', $this->bodyOptions);
    }

    // --------------------------------------------------------------------
    public function run()
    {
        echo Html::endTag('div');   //body
        if ($this->afterBody)
            echo $this->afterBody;
        if ($this->loading) {
            echo Html::tag('div', Html::icon(['refresh', 'spin']), ['class' => 'overlay']);
        }
        $this->_initFooter();
        echo Html::endTag('div');   // box
    }

    private function _initHeaderTools()
    {
        $headerToolsClass = [ArrayHelper::remove($this->headerToolsOptions, 'class', 'box-tools pull-right')];
        Html::addCssClass($this->headerToolsOptions, $headerToolsClass);
        echo Html::beginTag('div', $this->headerToolsOptions);
        foreach ($this->tools_order as $toolBtn) {
            if(in_array($toolBtn,$this->tools_btn_disabled)) continue;
            if ($toolBtn === 'collapse' && $this->tools_btn_collapse) {
                $this->headerTools['collapse'] = Html::button(
                    Html::icon(($this->isCollapsed ? 'plus' : 'minus')), $this->tools_btn_collapse);
            }
            elseif ($toolBtn === 'maximize' && $this->tools_btn_maximize) {
                $this->headerTools['maximize'] = Html::button(Html::icon('expand'), $this->tools_btn_maximize);
            }
            elseif ($toolBtn === 'remove' && $this->tools_btn_remove) {
                $this->headerTools['remove'] = Html::button(Html::icon('times'), $this->tools_btn_remove);
            }
            elseif ($toolBtn === 'refresh' && $this->tools_btn_refresh) {
                $this->headerTools['refresh'] = Html::button(Html::icon('sync-alt'), $this->tools_btn_refresh);
            }else {
                $this->initToolBtn($toolBtn);
            }
        }
//        if (in_array('collapse', $this->tools_order)) {
//            $this->headerTools['collapse'] = Html::button(
//                Html::icon(($this->isCollapsed ? 'plus' : 'minus')),
//                [
//                    'class' => 'btn btn-box-tool',
//                    'data-widget' => 'collapse'
//                ]
//            );
//        }
//        if (in_array('remove', $this->tools_order)) {
//            $this->headerTools['remove'] = Html::button(
//                Html::icon('times'),
//                [
//                    'class' => 'btn btn-box-tool',
//                    'data-widget' => 'remove'
//                ]
//            );
//        }
        foreach ($this->tools_order as $name) {
            if (isset($this->headerTools[$name]))
                echo $this->headerTools[$name];
        }
        echo Html::endTag('div');
    }

    private function _initFooter()
    {
        if ($this->footer) {
            Html::addCssClass($this->footerOptions, 'box-footer');
            echo Html::beginTag('div', $this->footerOptions);
            echo $this->footerContent;
            echo Html::endTag('div');
        }
    }

    protected function initToolBtn($btnType)
    {

        $icon = ($this->isCollapsed ? 'plus' : 'minus');
        if ($btnType === 'remove')
            $icon = 'times';
        $this->headerTools[$btnType] = Html::button(
            Html::icon($icon),
            [
                'class' => 'btn btn-box-tool',
                'data-widget' => $btnType
            ]
        );
    }

    protected function typeClass()
    {
        return 'box-' . $this->type;
    }

}