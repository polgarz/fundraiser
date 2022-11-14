<?php

use yii\db\Migration;

/**
 * Class m221114_185837_remove_editor_role
 */
class m221114_185837_remove_editor_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%user}}', ['group' => null], ['group' => 'editor']);
        $this->alterColumn('{{%user}}', 'group', "ENUM('admin')");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221114_185837_remove_editor_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221114_185837_remove_editor_role cannot be reverted.\n";

        return false;
    }
    */
}
