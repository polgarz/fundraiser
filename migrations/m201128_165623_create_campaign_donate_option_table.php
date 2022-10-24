<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%campaign_donate_option}}`.
 */
class m201128_165623_create_campaign_donate_option_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%campaign_donate_option}}', [
            'id' => $this->primaryKey(),
            'campaign_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'value' => $this->integer()->notNull(),
            'order' => $this->integer(),
        ]);

        $this->addForeignKey('fk_campaign_donate_option_campaign_id_campaign_id', '{{%campaign_donate_option}}', 'campaign_id', '{{%campaign}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%campaign_donate_option}}');
    }
}
