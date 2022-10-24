<?php
use yii\helpers\Url;

$url = Url::to(['admin/ambassador/approve', 'id' => $model->id], true);
?>

<h3>Szia!</h3>
<p>
    <strong><?= $model->user->fullname ?></strong> jelentkezett nagykövetnek a(z) <strong><?= $model->campaign->title ?></strong> kampányunkra
</p>

<p>
    Kérlek nézd át az alábbi kampányt, és ha megfelelőnek itéled, kattints a lenti engedélyez gombra!
</p>

<p>
<div><strong><?= $model->getAttributeLabel('name') ?></strong></div>
<div><?= $model->name ?></div>
<div><strong><?= $model->getAttributeLabel('lead') ?></strong></div>
<div><?= $model->lead ?></div>
<div><strong><?= $model->getAttributeLabel('image') ?></strong></div>
<div><img src="<?= Url::to($model->getThumbUploadUrl('image', 'list'), true) ?>" alt="" /></div>
<div><strong><?= $model->getAttributeLabel('goal') ?></strong></div>
<div><?= Yii::$app->formatter->asCurrency($model->goal) ?></div>
<div><strong><?= $model->getAttributeLabel('content') ?></strong></div>
<div><?= nl2br($model->content) ?></div>
<div><strong><?= $model->getAttributeLabel('tshirt_size') ?></strong></div>
<div><?= $model->tshirt_size ?></div>
<div><strong><?= $model->getAttributeLabel('address') ?></strong></div>
<div><?= $model->address ?></div>
</p>

<p>
    <a href="<?= $url ?>" style="display: block; padding: 20px; width: 200px; text-align: center; background-color: green; color: white">
        Engedélyezem
    </a>
</p>

<p>
    Legyen gyönyörű napod!
</p>