<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

/* @var $this \yii\web\View */

/* @var $content string */

use yihai\core\theming\Modal;
use yihai\core\web\Menu;
use yihai\core\theming\Html;
use yihai\core\web\NotificationFormat;
use yihai\core\helpers\Url;

/** @var \yihai\core\themes\defyihai\Theme $activeTheme */
$activeTheme = Yihai::$app->theme->activeTheme;
$mainAssetBundle = $activeTheme->mainAssets();
if(is_string($mainAssetBundle)) {
    $mainAssetBundle = $mainAssetBundle::register($this);
}
$content_title = ($this->title ? $this->title : '-');
$this->title = ($this->title ? $this->title . ' | Backend | ' . Yihai::$app->name : 'Backend | ' . Yihai::$app->name);

/** @var \yihai\core\assets\AppAsset $appAssetClass */
if(isset(Yihai::$app->params['AppAssetClass']))
    $appAssetClass = Yihai::$app->params['AppAssetClass'];
$appAsset = $appAssetClass::register($this);
$skin = (isset(Yihai::$app->params['___settings']) && Yihai::$app->params['___settings']['skin']) ? Yihai::$app->params['___settings']['skin'] : $activeTheme->skin;
$mainClass = Yihai::$app->controller->module->id . '-'.Yihai::$app->controller->id .'-'.Yihai::$app->controller->action->id;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shotcut icon" href="<?= Url::to('@web/favicon.ico') ?>" type="image/x-icon"/>
    <?php $this->head() ?>
</head>
<body class="<?=$mainClass?> hold-transition <?= $skin ?> sidebar-mini fixed">
<?php $this->beginBody() ?>

<div class="wrapper">

    <header class="main-header">
        <a href="<?= Url::to(['/system/index']) ?>" class="logo">
            <span class="logo-mini"><?= Html::icon('home') ?></span>
            <span class="logo-lg"><?= Yihai::$app->id ?></span>
        </a>

        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <?php if ($notif_pinned = Yihai::$app->notification->all(NotificationFormat::TYPE_PINNED)): ?>
                    <li class="dropdown messages-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
                           title="<?= Yihai::t('yihai', 'Notifikasi disematkan'); ?>">
                            <i class="fal fa-exclamation"></i>
                            <span class="label label-default"><?= count($notif_pinned) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header"><i
                                        class="fal fa-exclamation"></i> <?= Yihai::t('yihai', 'Notifikasi disematkan'); ?>
                            </li>
                            <li>
                                <ul class="menu">
                                    <?php
                                    foreach ($notif_pinned as $notif) {
                                        if ($notif->url)
                                            $url = $notif->url;
                                        else
                                            $url = Url::to(['/system/notifications/read/', 'id' => $notif->id]);
                                        echo ' <li>';
                                        echo '';
                                        echo '
                                        <a href="' . $url . '">
                                        <div class="pull-left">' . ($notif->icon ? Html::icon($notif->icon, ['prefix' => 'fal fa-', 'size' => 2]) : '') . '</div>
                                        <h4>' . $notif->title . '</h4>
                                        <p style="white-space: normal">' . $notif->text . '</p>
                                        </a>
                                    </li>';
                                    }
                                    ?>
                                </ul>
                            </li>

                            <?php /**<li class="footer"><a
                                        href="<?= Url::to(['/system/notifications']) ?>"><?= Yihai::t('yihai', 'View all'); ?></a>
                            </li> */?>
                        </ul>
                        <?php endif; ?>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            <img src="<?= Yihai::$app->user->identity->model->avatarUrl() ?>" class="user-image"
                                 alt="User Image">
                            <span class="hidden-xs"><?= Yihai::$app->user->identity->model->username ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <img src="<?= Yihai::$app->user->identity->model->avatarUrl() ?>" class="img-circle"
                                     alt="<?=Yihai::$app->user->identity->model->username?>">

                                <p>
                                    <b><?= Yihai::$app->user->identity->data->fullname ?></b>
                                    <small>
                                    <?=Yihai::$app->user->identity->model->group?>
                                    </small>
                                    <small><?= Yihai::t('yihai', 'Anggota sejak') ?>
                                        : <?= Yihai::$app->user->identity->model->memberSince ?></small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?= Url::to(['/system/profile']) ?>" class="btn btn-default btn-flat"><?=Yihai::t('yihai','Profil')?></a>
                                </div>
                                <div class="pull-right">
                                    <?= Html::beginForm(['/system/logout'], 'post')
                                    . Html::submitButton(
                                        Yihai::t('yihai', 'Keluar'),
                                        ['class' => 'btn btn-default btn-flat']
                                    )
                                    . Html::endForm() ?>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fal fa-cogs"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <aside class="main-sidebar">
        <div class="site-info">
            <div class="logo"><img src="<?= $appAsset->getLogoUrl() ?>" style="" class=""
                                   alt="Logo">
            </div>
            <div class="name"><?= Yihai::$app->name ?></div>
        </div>
        <section class="sidebar">

            <?php
            if ($backendMenu = Menu::getMenu('backend')) {
                echo \yihai\core\theming\SidebarMenu::widget([
                    'items' => $backendMenu
                ]);
            }
            ?>
        </section>
    </aside>
    <div class="content-wrapper">

        <section class="content-header">
            <h1>
                <?php

                if (isset($this->params['hints'])) {
                    echo \yihai\core\theming\Button::widget([
                        'encodeLabel' => false,
                        'label' => Html::icon('info'),
                        'size' => \yihai\core\theming\Button::SIZE_XS,
                        'type' => 'info',
                        'clientEvents' => [
                            'click' => 'function(){
                                    $("#main-hint-info").toggle();
                                }'
                        ]
                    ]);
                } ?>
                <?= $content_title ?>
                <?php
                $helpItems = isset(Yihai::$app->params['helpItems']) ? Yihai::$app->params['helpItems'] : '';
                $helpItem = (isset($helpItems[$this->context->id]) ? $helpItems[$this->context->id] : null);
                $infoIcon = '<small>' . Html::icon('info-circle') . '</small>';
                if ($helpItem) {
                    echo Html::a($infoIcon, '', ['title' => 'Bantuan', 'class' => 'chelp-link']);
                }
                ?>
                <small><?= isset($this->params['titleDesc']) ? $this->params['titleDesc'] : '' ?></small>
            </h1>
            <?= \yihai\core\theming\Breadcrumbs::widget([
                'homeLink' => [
                    'label' => 'System',
                    'url' => ['/system']
                ],
                'tag' => 'ol',
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        </section>
        <section class="content-header crud-hint">
            <?php

            if (isset($this->params['hints'])) {
                $hint = Html::ul($this->params['hints']);
                echo Html::beginTag('div', ['id' => 'main-hint-info', 'style' => 'display:none']);
                echo \yihai\core\theming\Alert::widget([
                    'type' => 'info',
                    'title' => Yihai::t('yihai', 'Petunjuk / Info'),
                    'icon' => Html::icon('info', ['class' => 'icon']),
                    'closeButton' => false,
                    'body' => $hint
                ]);
                echo Html::endTag('div');
            }
            ?>
        </section>
        <section class="content">
            <?php
            \yihai\core\theming\Alert::fromFlash(\yihai\core\theming\Alert::KEY_CRUD);
            ?>
            <?= $content ?>
        </section>
    </div>
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version:</b> Yihai <?= Yihai::$version ?>, App <?= Yihai::$app->version ?>
        </div>
        <?= Yihai::$app->copyright ?>
    </footer>
    <aside class="control-sidebar control-sidebar-light">
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            <li class="active"><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fal fa-cog"></i></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="control-sidebar-settings-tab">
                <?=Html::beginForm('','POST',['id'=>'control-sidebar-settings-form']);?>
                <?=Html::hiddenInput('___settings', 1);?>
                <?=Html::hiddenInput('skin', $skin, ['id'=>'settings-form-skin-id']);?>
                    <div class="form-group">
                        <label for="switch-language" class="control-sidebar-subheading">
                            <?=Yihai::t('yihai','Bahasa')?>
                        </label>
                            <select id="switch-language" name="language" class="form-control">
                                <?php foreach (Yihai::$app->params['languageList'] as $key => $v){
                                echo "<option ".(Yihai::$app->language === $key ? 'selected':'')." value=\"{$key}\">{$v}</option>";
                                }
                                ?>
                            </select>
                    </div>
                <div class="form-group" id="skins-list">
                </div>
                <button class="btn btn-primary btn-block"><?=Yihai::t('yihai','Simpan')?></button>
                <?=Html::endForm();?>
            </div>
        </div>
    </aside>
</div>
<?php

$overlay = '<div class="overlay"><i class="fa fa-spin fa-spinner"></i></div>';
Modal::begin([
    'id' => 'yihai-crud-basemodal',
    'header' => '<div class="text-bold"><i class="far fa-pen-square"></i> <span class="modal-title"></span></div>',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => ['backdrop' => 'static'],
    'clientEvents' => [
//        'shown.bs.modal' => 'function(event){var modaltype = {insert:"bg-green",update:"bg-blue",view:"bg-white",delete:"bg-red"};var href=$(event.relatedTarget).attr("href");$(this).find(".modal-body").load(href);$(this).find(".modal-title").text($(event.relatedTarget).attr("title"));$(this).find(".modal-header").addClass(modaltype[$(event.relatedTarget).attr("data-modal-type")]);}',
        'shown.bs.modal' => /** @lang javascript */ 'function(event){
        var _modal = $(this);
        var href=$(event.relatedTarget).attr("href");
        $.ajax({
            url:href,
            success: function(d){
                var formTitle = d.formTitle ? d.formTitle : $(event.relatedTarget).attr("title");
                _modal.find(".modal-title").html(formTitle);
                _modal.find(".modal-body").html(d.html)
            },
            error:function(d){
                _modal.find(".modal-title").html(d.statusText);
                _modal.find(".modal-body").html("<a href=\""+href+"\">Show Error</a>")
            }
        });
    }',
        'hidden.bs.modal' => 'function(event){$(this).find(".modal-body").html(\'' . $overlay . '\');$(this).find(".modal-title").text("");$(this).find(".modal-header").removeClass("bg-green").removeClass("bg-blue").removeClass("bg-red").removeClass("bg-white")}'
    ],
]);
echo $overlay;
Modal::end();
$deleteFooter = Html::beginForm([''], 'post', ['id' => 'yihai-crud-basemodal-deleteform'])
    . Html::hiddenInput('multiple')
    . Html::submitButton(
        Yihai::t('yihai', 'Ya'),
        ['class' => 'btn btn-danger']
    ) . Html::button(Yihai::t('yihai', 'Tidak'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal'])
    . Html::endForm();

Modal::begin([
    'id' => 'yihai-crud-basemodal-delete',
    'headerOptions' => ['class' => 'bg-red'],
    'header' => '<div class="text-bold"><i class="fal fa-pen-square"></i> <span class="modal-title"></span>' . Yihai::t('yihai', 'Hapus item.') . '</div>',
    'clientOptions' => ['backdrop' => 'static'],
    'footer' => $deleteFooter,
    'size' => Modal::SIZE_SMALL,
    'clientEvents' => [
        'show.bs.modal' => 'function(event){
        var href=$(event.relatedTarget).attr("href");
        var multiple =  $(event.relatedTarget).attr("data-multiple");
        if(multiple){
//            href = href+"?yihai_multiple="+multiple;
        $(this).find("#yihai-crud-basemodal-deleteform").find(\'input[name="multiple"]\').val((multiple));
            $(this).find(".delete-text-info").text("' . Yihai::t('yihai', 'Anda yakin ingin menghapus item yang dipilih?') . '");
        }else{
            $(this).find(".delete-text-info").text("' . Yihai::t('yihai', 'Anda yakin ingin menghapus item ini?') . '");
        }
        $(this).find("#yihai-crud-basemodal-deleteform").attr("action", href)
    }',
    ],
]);
echo Html::tag('div', '', ['class' => 'delete-text-info']);
Modal::end();
$this->registerJs("
$('.content a[title], .content button[title]').tooltip({ trigger: 'hover' });
jQuery(document).on(\"pjax:success\",  function(event){
    $('.content a[title], .content button[title]').tooltip({ trigger: 'hover' });
});
");
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
