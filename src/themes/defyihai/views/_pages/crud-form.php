<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\helpers\Url;
use yihai\core\theming\ActiveForm;
use yihai\core\theming\BoxCard;
use yihai\core\theming\Html;
use yii\helpers\ArrayHelper;

/** @var \yihai\core\web\View $this */
/** @var string $formViewFile */
/** @var \yihai\core\base\ModelOptions $modelOptions */
/** @var string $formType */
/** @var string $formButton */
/** @var string $formTitle */
/** @var string $title */
/** @var array $_params */

$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $formTitle;

$_isAjax = Yihai::$app->request->getIsAjax() || Yihai::$app->request->getIsPjax();
$this->title = $this->title . ' - ' . $formType;
$form = ActiveForm::begin(ArrayHelper::merge([
    'id' => 'form-' . str_replace('/', '-', $this->context->getUniqueId()) . '-' . $this->context->action->id,
    'layout' => ($_isAjax ? 'default' : 'horizontal'),
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ],
], $modelOptions->formConfig));

$modelOptions->viewParams['form'] = $form;
$content = $this->renderFile($formViewFile, $modelOptions->viewParams);


$saveBtn = Html::submitButton(Html::icon('save') . ' ' . $formButton,
    ['class' => ['btn', 'btn-success']]
);

$saveContinueEditBtn = Html::submitButton(Html::icon('save') . ' ' . Yihai::t('yihai', '{formAction} and continue edit',['formAction'=>$formButton]),
    ['class' => ['btn', 'btn-primary'], 'formaction'=>Url::current(['__redirect'=>Url::current()])]
);
if ($_isAjax) {
    $cancelBtn = Html::button(Html::icon('undo') . ' ' . Yihai::t('yihai', 'Cancel'),
        ['class' => ['btn', 'btn-default'], 'data-dismiss' => 'modal']
    );
    echo $content;
    echo $saveBtn . ' ' . ($modelOptions->formButtonContinueEdit ? $saveContinueEditBtn : '').' ' . $cancelBtn;
} else {
    $cancelBtn = Html::a(Html::icon('undo') . ' ' . Yihai::t('yihai', 'Cancel'),
        $modelOptions->redirect,
        ['class' => ['btn', 'btn-default']]
    );

    BoxCard::begin([
        'type' => 'primary',
        'footer' => true,
        'tools_order' => ['collapse'],
        'title' => $formTitle,
        'footerContent' => $saveBtn . ' ' . ($modelOptions->formButtonContinueEdit ? $saveContinueEditBtn : '').' ' . $cancelBtn
    ]);
    echo $content;
    BoxCard::end();
}

ActiveForm::end();