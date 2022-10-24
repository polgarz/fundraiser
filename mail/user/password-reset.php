<?php
use yii\helpers\Url;
?>

<h3>Kedves <?= $model->user->fullname ?>!</h3>
<p>A jelszó alaphelyzetbe állításához szükséges kód a következő: <?= $model->user->password_reset_code ?>.</p>