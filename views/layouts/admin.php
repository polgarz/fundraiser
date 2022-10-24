<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Breadcrumbs;
use app\assets\AdminAsset;
use app\models\user\User;
use diecoding\toastr\ToastrFlash;

AdminAsset::register($this);

$this->registerJs('feather.replace()');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow navbar-expand-md">

        <a class="navbar-brand col-md-3 col-lg-2 col-sm-auto col-auto mr-0" href="<?= Url::to(['admin/user/index']) ?>">
            Admin
        </a>
        <button class="navbar-toggler m-1 ml-2" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarToggler">
            <div class="w-100">
                <!-- empty :( -->
            </div>
            <?= Nav::widget([
                'options' => ['class' => 'navbar-nav px-3'],
                'encodeLabels' => false,
                'items' => [
                    '<h6 class="sidebar-heading d-flex justify-content-between align-items-center mb-1 mt-2 d-md-none text-muted"><span>Adminisztráció</span></h6>',
                    ['label' => '<i class="fa fa-user"></i> Felhasználók', 'url' => ['admin/user/index'], 'options' => ['class' => 'nav-item d-md-none text-nowrap'], 'visible' => Yii::$app->user->can(User::GROUP_ADMIN)],
                    ['label' => '<i class="fa fa-heart"></i> Kampányok', 'url' => ['admin/campaign/index'], 'options' => ['class' => 'nav-item d-md-none text-nowrap'], 'visible' => Yii::$app->user->can(User::GROUP_ADMIN) || Yii::$app->user->can(User::GROUP_FUNDRAISER)],
                    ['label' => '<i class="fa fa-dollar-sign"></i> Adományok', 'url' => ['admin/donation/index'], 'options' => ['class' => 'nav-item d-md-none text-nowrap'], 'visible' => Yii::$app->user->can(User::GROUP_ADMIN)],
                    ['label' => '<i class="fa fa-sign-out-alt"></i> Kijelentkezés', 'url' => ['user/logout'], 'options' => ['class' => 'nav-item text-nowrap'], 'linkOptions' => ['data-method' => 'post'], 'visible' => !Yii::$app->user->isGuest],
                ],
            ]) ?>
        </div>
    </nav>

    <?= ToastrFlash::widget(['showMethod' => 'show', 'hideMethod' => 'hide', 'timeOut' => 3000, 'progressBar' => false]) ?>

    <div class="container-fluid">
        <div class="row">
            <?php NavBar::begin([
                'brandLabel' => false,
                'options' => ['class' => 'col-lg-2 col-md-3 d-none d-md-block bg-light sidebar'],
                'collapseOptions' => ['class' => 'sidebar-sticky show'],
                'togglerOptions' => ['style' => 'display: none'],
                'renderInnerContainer' => false,
            ]) ?>

            <?= Nav::widget([
                'options' => ['class' => 'nav flex-column'],
                'encodeLabels' => false,
                'items' => [
                    '<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-1 text-muted"><span>Adminisztráció</span></h6>',
                    ['label' => '<i class="fa fa-user"></i> Felhasználók', 'url' => ['admin/user/index'], 'options' => ['class' => 'text-nowrap'], 'visible' => Yii::$app->user->can(User::GROUP_ADMIN)],
                    ['label' => '<i class="fa fa-heart"></i> Kampányok', 'url' => ['admin/campaign/index'], 'options' => ['class' => 'text-nowrap'], 'visible' => Yii::$app->user->can(User::GROUP_ADMIN) || Yii::$app->user->can(User::GROUP_FUNDRAISER)],
                    ['label' => '<i class="fa fa-dollar-sign"></i> Adományok', 'url' => ['admin/donation/index'], 'options' => ['class' => 'text-nowrap'], 'visible' => Yii::$app->user->can(User::GROUP_ADMIN)],
                ],
            ]) ?>

            <?php NavBar::end() ?>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-3">
                <?php if (!isset($this->params['hideHeader'])): ?>
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                        <h1 class="h2"><?= $this->title ?></h1>
                        <div class="mb-2 mb-md-0">
                            <?= Breadcrumbs::widget([
                                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                            ]) ?>
                        </div>
                    </div>
                <?php endif ?>

                <?= $content ?>
            </main>
        </div>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
