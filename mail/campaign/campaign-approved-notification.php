<?php
use yii\helpers\Url;

$url = Url::to(['campaign/ambassador', 'campaign_slug' => $model->campaign->slug, 'slug' => $model->slug], true);
?>

<h3>Kedves <?= $model->user->last_name ?>!</h3>
<p>
    Köszönjük, hogy gyűjtesz nekünk! A kampányodat átnéztük, és tökéletesnek találtuk, így engedélyeztük.
</p>

<p>
    <a href="<?= $url ?>">Ide kattintva</a> megtekintheted a kampányodat. Reméljük sikeres lesz a gyűjtésed!
</p>