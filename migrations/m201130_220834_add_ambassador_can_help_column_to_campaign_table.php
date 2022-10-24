<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%campaign}}`.
 */
class m201130_220834_add_ambassador_can_help_column_to_campaign_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{campaign}}', 'ambassador_can_apply', $this->boolean()->after('cover_image'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{campaign}}', 'ambassador_can_apply');
    }
}
