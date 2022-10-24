<?php
namespace app\widgets;

use yii\base\Widget;
use app\models\campaign\Campaign;

class HighlightedCampaignWidget extends Widget
{
    public function run()
    {
        $model = Campaign::find()
            ->where(['highlighted' => 1, 'status' => Campaign::STATUS_PUBLIC])
            ->orderBy('rand()')
            ->limit(1)
            ->one();

        if ($model) {
            return $this->render('highlighted-campaign', [
                'model' => $model
            ]);
        }
    }
}
