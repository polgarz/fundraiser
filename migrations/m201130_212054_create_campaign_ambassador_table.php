<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%campaign_ambassador}}`.
 */
class m201130_212054_create_campaign_ambassador_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%campaign_ambassador}}', [
            'id' => $this->primaryKey(),
            'campaign_id' => $this->integer()->notNull(),
            'user_id' => $this->integer(),
            'slug' => $this->string()->notNull(),
            'lead' => $this->text(),
            'image' => $this->string(),
            'goal' => $this->integer(),
            'content' => $this->text(),
            'approved' => $this->boolean(),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB');

        $this->addForeignKey('fk_campaign_ambassador_campaign_id_campaign_id', '{{%campaign_ambassador}}', 'campaign_id', '{{%campaign}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_campaign_ambassador_user_id_user_id', '{{%campaign_ambassador}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%campaign_ambassador}}');
    }
}
