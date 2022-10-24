<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%campaign_ambassador}}`.
 */
class m201201_201930_add_name_column_to_campaign_ambassador_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{campaign_ambassador}}', 'name', $this->string()->after('user_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{campaign_ambassador}}', 'name');
    }
}
