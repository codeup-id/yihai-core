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
/** @var \yihai\core\models\form\LoginForm $model */

$this->title = Yihai::$app->name;
?>
<div class="login-box">
    <div class="login-logo">
        <b><?= Yihai::$app->name ?></b>
    </div>
    <div class="login-box-body">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
//            'layout' => 'horizontal',
            'options' => [
//                'class'=>'form-horizontal'
            ],
            'fieldConfig' => [
//                'template' => "<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
//                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>

        <?= $form->field($model, 'username', [
            'options'=>['class'=>'form-group has-feedback'],
            'template' => '{input}{error}<span class="fal fa-user form-control-feedback"></span>'])->textInput(['autofocus' => true, 'placeholder' => Yihai::t('yihai', 'Nama pengguna/Email')]) ?>

        <?= $form->field($model, 'password',[
            'options'=>['class'=>'form-group has-feedback'],
            'template' => '{input}{error}<span class="fal fa-key form-control-feedback"></span>'])->passwordInput(['placeholder'=>Yihai::t('yihai','Kata sandi')]) ?>
        <?php
        if($group && isset(Yihai::$app->user->groupClass[$group])){
            echo $form->field($model, 'group', ['template'=>'{input}Group: '.Inflector::camel2words($group)])->hiddenInput(['value'=>$group]);
        }else {
            $groups = [];
            foreach (Yihai::$app->user->groupClass as $name => $class) {
                $groups[$name] = Inflector::camel2words($name);
            }
            echo $form->field($model, 'group',['template'=>'{input}{error}'])->widget(\yihai\core\extension\select2\Select2::class,['items'=>$groups,'options'=>['placeholder'=>'Group']]);
        }
        ?>
        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe', ['template'=>'{input}'])->widget(ICheck::class,[
                    'color' => 'green',
                    'skin' => ICheck::SKIN_FLAT,
                ])?>
            </div>
            <div class="col-xs-4">
                <?= Html::submitButton(Yihai::t('yihai','Masuk'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>



