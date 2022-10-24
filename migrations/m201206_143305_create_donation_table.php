<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%donation}}`.
 */
class m201206_143305_create_donation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%donation}}', [
            'id' => $this->primaryKey(),
            'hash' => $this->string()->unique()->notNull(),
            'user_id' => $this->integer(),
            'payment_method' => "ENUM('transfer','card','cash') NOT NULL",
            'amount' => $this->integer(),
            'name' => $this->string()->notNull(),
            'anonymous' => $this->boolean()->defaultValue(0),
            'email' => $this->string()->notNull(),
            'message' => $this->string(512),
            'newsletter' => $this->boolean()->defaultValue(0),
            'campaign_id' => $this->integer(),
            'campaign_ambassador_id' => $this->integer(),
            'status' => $this->string()->notNull(),
            'vendor_ref' => $this->integer(),
            'recurring' => $this->boolean()->defaultValue(0),
            'parent_id' => $this->integer(),
            'token' => $this->string(),
            'token_due_date' => $this->datetime(),
            'created_at' => $this->datetime(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB');

        $this->addForeignKey('fk_donation_user_id_user_id', '{{%donation}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_donation_campaign_ambassador_id_campaign_ambassador_id', '{{%donation}}', 'campaign_ambassador_id', '{{%campaign_ambassador}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_donation_campaign_id_campaign_id', '{{%donation}}', 'campaign_id', '{{%campaign}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_donation_parent_id_donation_id', '{{%donation}}', 'parent_id', '{{%donation}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%donation}}');
    }
}
