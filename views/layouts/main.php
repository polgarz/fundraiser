<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use lajax\languagepicker\widgets\LanguagePicker;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/dist/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/dist/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/dist/favicon/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="/dist/favicon/favicon.ico">
    <link rel="manifest" href="/dist/favicon/site.webmanifest">
    <link rel="mask-icon" href="/dist/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:url" content="<?= Url::current([], true) ?>" />
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Annie+Use+Your+Telescope&family=Open+Sans:wght@300;400;600&family=Oswald:wght@400;500&display=swap" as="style">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Annie+Use+Your+Telescope&family=Open+Sans:wght@300;400;600&family=Oswald:wght@400;500&display=swap">
</head>
<body>
<?php $this->beginBody() ?>
    <nav class="navbar navbar-expand navbar-dark upper-nav">
        <div class="upper-nav-wrapper d-flex justify-content-end mx-auto">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="<?= Url::to(['user/profile']) ?>" class="nav-link navbar-icon user mr-3"></a>
                </li>
                <?= LanguagePicker::widget([
                    'itemTemplate' => '<a class="dropdown-item" href="{link}">{name}</a>',
                    'activeItemTemplate' => '<a href="{link}" class="nav-link dropdown-toggle p-0" title="{language}"><i class="{language}"></i> {name}</a>',
                    'parentTemplate' => '<li class="nav-link p-0 dropdown">{activeItem}<div class="dropdown-menu">{items}</div></li>',
                ]) ?>
            </ul>
        </div>
    </nav>

    <nav class="main-nav navbar navbar-expand-sm navbar-light bg-white d-flex sticky-top justify-content-end">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Menü">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="main-nav-wrapper d-flex justify-content-sm-start">
            <a href="<?= Yii::$app->homeUrl ?>" class="position-fixed logo">
                <img src="/dist/svg/logo.svg" alt="Fundraiser">
            </a>

            <div id="navbarToggler" class="collapse navbar-collapse pl-sm-8">

            </div>
        </div>
    </nav>

    <div class="container-fluid px-0">

        <?php foreach(Yii::$app->session->getAllFlashes() as $key => $message): ?>
            <div class="alert text-center alert-<?= $key ?>"><?= $message ?></div>
        <?php endforeach ?>

        <?= $content ?>
    </div>
    <footer>
        <section class="center-section mx-auto py-5">
            <div class="row p-0 m-0 text-center">
                <div class="col-md-6">
                    <h5 class="text-uppercase mb-3"><?= Yii::t('footer', 'Kapcsolat') ?></h5>
                    <ul class="list-unstyled">
                        <li><strong><?= Yii::t('footer', 'Fundraiser') ?></strong></li>
                    </ul>
                </div>
                <div class="col-md-6">
                </div>
            </div>

            <hr />

            <div class="row m-0">
                <div class="col-md-6">
                    <div class="small px-3">
                        &copy; <?= date('Y') ?> <?= Yii::t('footer', 'Minden jog fenntartva') ?> - <?= Yii::$app->name ?>
                    </div>
                </div>
            </div>
        </section>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
