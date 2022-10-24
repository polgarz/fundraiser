<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%campaign_tag}}`.
 */
class m201128_165629_create_campaign_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%campaign_tag}}', [
            'id' => $this->primaryKey(),
            'campaign_id' => $this->integer(),
            'tag_id' => $this->integer(),
            'order' => $this->integer(),
        ], 'ENGINE=InnoDB');

        $this->addForeignKey('fk_campaign_tag_campaign_id_campaign_id', '{{%campaign_tag}}', 'campaign_id', '{{%campaign}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_campaign_tag_tag_id_tag_id', '{{%campaign_tag}}', 'tag_id', '{{%tag}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%campaign_tag}}');
    }
}
