<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m200615_200525_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'phone' => $this->string(),
            'group' => "ENUM('admin', 'editor')",
            'password' => $this->string()->notNull(),
            'auth_key' => $this->string()->notNull(),
            'activation_hash' => $this->string(),
            'password_reset_code' => $this->integer(),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ], 'ENGINE=InnoDB');

        $this->insert('user', [
            'id' => 1,
            'first_name' => 'Polgár',
            'last_name' => 'Zoltán',
            'email' => 'admin@admin.com',
            'password' => Yii::$app->security->generatePasswordHash('admin'),
            'group' => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'auth_key' => Yii::$app->security->generateRandomString(64)]);

        $this->addForeignKey('fk_user_created_by_user_id', '{{%user}}', 'created_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_user_updated_by_user_id', '{{%user}}', 'updated_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
