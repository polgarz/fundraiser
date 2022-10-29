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
    <nav class="main-nav navbar navbar-expand navbar-light bg-white d-flex sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= Yii::$app->homeUrl ?>">
                <img src="/dist/svg/logo.svg" height="30" class="d-inline-block align-top mr-1" alt="<?= Yii::$app->name ?>" />
            </a>

            <ul class="navbar-nav align-items-center flex-row">
                <li class="nav-item">
                    <a href="<?= Url::to(['user/profile']) ?>" class="nav-link navbar-icon user mr-3"></a>
                </li>
                <?= LanguagePicker::widget([
                    'itemTemplate' => '<a class="dropdown-item" href="{link}">{name}</a>',
                    'activeItemTemplate' => '<a href="{link}" class="nav-link dropdown-toggle p-0" title="{language}"><i class="{language}"></i> {name}</a>',
                    'parentTemplate' => '<li class="nav-link dropdown">{activeItem}<div class="dropdown-menu">{items}</div></li>',
                ]) ?>
            </ul>
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
                        <li><a href="<?= Yii::$app->params['privacyPolicyUrl'] ?>"><?= Yii::t('footer', 'Adatvédelmi nyilatkozat') ?></a></li>
                    </ul>
                </div>
            </div>

            <hr />

            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="small">
                        &copy; <?= date('Y') ?> <?= Yii::t('footer', 'Minden jog fenntartva') ?> - <?= Yii::$app->name ?>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <a href="https://www.buymeacoffee.com/polgarz" target="_blank">
                        <img src="https://cdn.buymeacoffee.com/buttons/v2/default-white.png" alt="Buy Me A Coffee" style="height: 40px !important;">
                    </a>
                </div>
            </div>
        </section>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
