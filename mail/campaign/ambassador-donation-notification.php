<?php
use yii\helpers\Url;

$url = Url::to(['campaign/ambassador', 'campaign_slug' => $model->ambassador->campaign->slug, 'slug' => $model->ambassador->slug], true);
?>

<h3>Kedves <?= $model->ambassador->user->last_name ?>!</h3>
<p>
    <?php if ($model->anonymous): ?>
        Valaki <?= Yii::$app->formatter->asCurrency($model->amount) ?> összeggel támogatta a kampányod.<br />
        <?php if (!empty($model->message)): ?>
            Az üzenete: <?= $model->message ?><br />
        <?php endif ?>
        Az illető szeretne névtelen maradni, ezért mi sem mondjuk el, hogy ki volt :)
    <?php else: ?>
        <strong><?= $model->name ?></strong> <?= Yii::$app->formatter->asCurrency($model->amount) ?> összeggel támogatta a kampányod. Köszönd meg neki a nevünkben is! :)
    <?php endif ?>
</p>

<p>
    Köszönjük, hogy gyűjtesz nekünk! Már most is sokat segítettél! Csak így tovább!
</p>

<p>
    <a href="<?= $url ?>">Ide kattintva</a> megtekintheted a kampányodat.
</p>