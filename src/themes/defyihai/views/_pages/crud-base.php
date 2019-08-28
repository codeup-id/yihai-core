<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\theming\Html;

/** @var \yihai\core\web\View $this */
/** @var string $viewFile */
/** @var \yihai\core\base\ModelOptions $modelOptions */

if (!Yihai::$app->request->getIsAjax() && !Yihai::$app->request->getIsPjax())
    $this->title = $modelOptions->getBaseTitle();
$content = $this->renderFile($viewFile, $modelOptions->viewParams);
echo $content;