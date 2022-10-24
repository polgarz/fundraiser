<?php

use yii\db\Migration;

/**
 * Class m201204_175051_add_highlight_field_to_campaign_table
 */
class m201204_175051_add_highlight_field_to_campaign_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%campaign}}', 'highlight_subtitle', $this->string()->after('highlight_image'));
        $this->addColumn('{{%campaign}}', 'highlight_text', $this->string(512)->after('highlight_subtitle'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%campaign}}', 'highlight_subtitle');
        $this->dropColumn('{{%campaign}}', 'highlight_text');
    }
}
