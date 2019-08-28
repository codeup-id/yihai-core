<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

/** @var $this \yihai\core\web\View */

/** @var bool $noData */

use yihai\core\theming\BoxCard;
use yihai\core\theming\Html;

$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'View';
echo Html::beginTag('div', ['class' => ('row')]);
echo Html::beginTag('div', ['class' => ('col-md-6')]);
if ($noData) {
    echo Html::tag('div', Yihai::t('yihai', 'Tidak ada data yang ditampilkan di sini.'));
} else {
    BoxCard::begin([
        'type' => 'primary',
        'tools_order' => [],
        'header' => false,
        'afterBody' => $detailViewData,

    ]);
    echo Html::tag('div', Html::icon('th-list') . ' ' . Yihai::t('yihai', 'Data'), ['class' => ('text-bold')]);
    BoxCard::end();
}
echo Html::endTag('div');
if (isset($detailViewCreatedUpdated)) {
    echo Html::beginTag('div', ['class' => ('col-md-6')]);
    BoxCard::begin([
        'type' => 'primary',
        'tools_order' => [],
        'header' => false,
        'afterBody' => $detailViewCreatedUpdated,

    ]);
    echo Html::tag('div', Html::icon('th-list') . ' ' . Yihai::t('yihai', 'Dibuat & Perbarui Info'), ['class' => ('text-bold')]);
    BoxCard::end();
    echo Html::endTag('div');
}

echo Html::endTag('div');

echo Html::beginTag('div', ['class' => ('row')]);
foreach ($detailViewCustom as $label => $custom) {
    echo Html::beginTag('div', ['class' => ('col-md-6')]);
    BoxCard::begin([
        'tools_order' => [],
        'header' => false,
        'afterBody' => $custom,

    ]);
    echo Html::tag('div', Html::icon('th-list') . ' ' .  $label, ['class' => ('text-bold')]);
    BoxCard::end();
    echo Html::endTag('div');
}
echo Html::endTag('div');