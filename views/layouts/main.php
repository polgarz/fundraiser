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
    <meta property="og:url" content="<?= Url::current([], true) ?>" />
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Annie+Use+Your+Telescope&family=Open+Sans:wght@300;400;600&family=Montserrat:wght@400;500&display=swap" as="style">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Annie+Use+Your+Telescope&family=Open+Sans:wght@300;400;600&family=Montserrat:wght@400;500&display=swap">
</head>
<body>
<?php $this->beginBody() ?>
    <nav class="main-nav navbar navbar-expand-sm navbar-light bg-white d-flex sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= Yii::$app->homeUrl ?>">
                <img src="/dist/svg/logo.svg" height="30" class="d-inline-block align-top mr-1" alt="<?= Yii::$app->name ?>" />
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Menü">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="navbarToggler" class="collapse navbar-collapse d-flex justify-content-between">
                <ul class="navbar-nav">
                </ul>
                <ul class="navbar-nav">
                    <?= LanguagePicker::widget([
                        'itemTemplate' => '<a class="dropdown-item" href="{link}">{name}</a>',
                        'activeItemTemplate' => '<a href="{link}" class="nav-link dropdown-toggle p-0" title="{language}"><i class="{language}"></i> {name}</a>',
                        'parentTemplate' => '<li class="nav-link dropdown">{activeItem}<div class="dropdown-menu">{items}</div></li>',
                    ]) ?>
                </ul>
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
        <section class="py-5 container">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-uppercase mb-3"><?= Yii::t('footer', 'Információk') ?></h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Adatvédelmi nyilatkozat</a></li>
                    </ul>
                </div>
            </div>

            <hr />

            <div class="row">
                <div class="col-md-6">
                    <div class="small">
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
