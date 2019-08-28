<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */


use yihai\core\theming\ActiveForm;
use yihai\core\theming\Button;
use yihai\core\theming\Grid;
use yihai\core\theming\Html;
use yii\rbac\Item;

/** @var \yihai\core\modules\system\models\AddRoleForm $model */
if ($model->isUpdating)
    $this->title = Yihai::t('yihai', 'Perbarui peran kustom');
else
    $this->title = Yihai::t('yihai', 'Tambah peran kustom');
$formDelete = '';
if ($model->isUpdating) {
    $formDelete = Html::beginForm() . Html::hiddenInput('_delete', $model->name) . Button::widget(['label' => Yihai::t('yihai', 'Hapus peran'), 'type' => 'danger']) . Html::endForm();
}
$htmlGrid = Grid::begin();
echo $htmlGrid->beginCol(['md-6']);
\yihai\core\theming\BoxCard::begin([
    'title' => $formDelete,
    'tools_order' => []


]);
$form = ActiveForm::begin([]);
echo $form->field($model, 'name');
echo $form->field($model, 'description')->textarea();
echo Button::widget([
        'label' => $model->isUpdating ? Yihai::t('yihai', 'Perbarui') : Yihai::t('yihai', 'Tambah'),
        'type' => 'primary'
    ]) . ' ';
echo Button::widget([
    'label' => Yihai::t('yihai', 'Batal'),
    'tag' => 'a',
    'options' => [
        'href' => \yihai\core\helpers\Url::to(['roles'])
    ]
]);
ActiveForm::end();
\yihai\core\theming\BoxCard::end();
$htmlGrid->endCol();
Grid::end();
