<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

/* @var $this \yii\web\View */
/* @var $content string */

use yihai\core\themes\defyihai\assets\MainAsset;
use yii\helpers\Html;

$mainAssetBundle = MainAsset::register($this);
$mainClass = Yihai::$app->controller->module->id . '-'.Yihai::$app->controller->id .'-'.Yihai::$app->controller->action->id;
?>
<?php $this->beginPage('system.php') ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="<?=$mainClass?>">
<?php $this->beginBody() ?>

<?php
echo $content;
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
