<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\theming\ActiveForm;
use yihai\core\theming\ICheck;
use yii\helpers\Html;
use yii\helpers\Inflector;

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $group string */
/* @var $header_text string */
/* @var $show_remember_checkbox bool */
/* @var $show_group bool */
/* @var $show_label_input bool */
/* @var $placeholder_user string */
/* @var $placeholder_pass string */
/** @var \yihai\core\models\form\LoginForm $model */

$this->title = Yihai::$app->name;
$this->registerCss('
body{background: #d2d6de;}
.login-header-text{
    border-bottom: 1px solid #ddd;
    width: 100%;
    background: #ffffff;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    padding: 10px 5px;
}
');
$appAsset = Yihai::registerAppAsset($this);
?>
<div class="login-box">
    <div class="login-logo">
        <img src="<?= $appAsset->getLogoUrl() ?>" style="width:100px;"/>
    </div>
    <div class="login-header-text">
        <?=$header_text?></div>
    <div class="login-box-body">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
        ]); ?>

        <?= $form->field($model, 'username', [
            'options'=>['class'=>'form-group has-feedback'],
            'template' => ($show_label_input ? '{label}':'').'{input}{error}<span class="fal fa-user form-control-feedback"></span>'])->textInput(['autofocus' => true, 'placeholder' => $placeholder_user]) ?>

        <?= $form->field($model, 'password',[
            'options'=>['class'=>'form-group has-feedback'],
            'template' => ($show_label_input ? '{label}':'').'{input}{error}<span class="fal fa-key form-control-feedback"></span>'])->passwordInput(['placeholder'=>$placeholder_pass]) ?>
        <?php
        if($show_group) {
            if ($group && isset(Yihai::$app->user->groupClass[$group])) {
                echo $form->field($model, 'group', ['template' => '{input}Group: ' . Inflector::camel2words($group)])->hiddenInput(['value' => $group]);
            } else {
                $groups = [];
                foreach (Yihai::$app->user->groupClass as $name => $class) {
                    $groups[$name] = Inflector::camel2words($name);
                }
                echo $form->field($model, 'group', ['template' => '{input}{error}'])->widget(\yihai\core\extension\select2\Select2::class, ['items' => $groups, 'options' => ['placeholder' => 'Group']]);
            }
        }
        ?>
        <div class="row">
            <div class="col-xs-8">
                <?= $show_remember_checkbox ? $form->field($model, 'rememberMe', ['template'=>'{input}'])->widget(ICheck::class,[
                    'color' => 'green',
                    'skin' => ICheck::SKIN_FLAT,
                ]) : ''?>
            </div>
            <div class="col-xs-4">
                <?= Html::submitButton(Yihai::t('yihai','Masuk'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>



