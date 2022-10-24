<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%campaign}}`.
 */
class m201127_231649_create_campaign_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%campaign}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'slug' => $this->string()->notNull()->unique(),
            'status' => "ENUM('draft', 'public') DEFAULT 'draft'",
            'lead' => $this->text(),
            'content' => 'mediumtext',
            'archive' => $this->boolean()->defaultValue(0),
            'goal' => $this->integer(),
            'meta_description' => $this->string(),
            'og_image' => $this->string(),
            'og_description' => $this->string(),
            'recurring_available' => $this->boolean(),
            'default_donation_type' => "ENUM('recurring', 'one-time')",
            'custom_donation_available' => $this->boolean(),
            'highlighted' => $this->boolean(),
            'highlight_image' => $this->string(),
            'cover_image' => $this->string(),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB');

        $this->addForeignKey('fk_campaign_created_by_user_id', '{{%campaign}}', 'created_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_campaign_updated_by_user_id', '{{%campaign}}', 'updated_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%campaign}}');
    }
}
