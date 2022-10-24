<?php
use yii\helpers\Url;
$url = Url::to(['user/activate', 'hash' => $model->user->activation_hash], true)
?>
<h3>Kedves <?= $model->first_name ?> <?= $model->last_name ?>!</h3>

<p>
    Már csak egy lépésre vagy a regisztrációtól.
    Az aktiváláshoz kattints az alábbi gombra
</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
    <tbody>
    <tr>
        <td align="left">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td> <a href="<?= $url ?>" target="_blank">Aktiválás</a> </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<p>
    Amennyiben a fenti gomb nem működik, másold be a böngészőbe az alábbi linket: <?= $url ?>
</p>