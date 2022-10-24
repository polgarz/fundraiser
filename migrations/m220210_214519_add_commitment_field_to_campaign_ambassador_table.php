<?php

use yii\db\Migration;

/**
 * Class m220210_214519_add_commitment_field_to_campaign_ambassador_table
 */
class m220210_214519_add_commitment_field_to_campaign_ambassador_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%campaign_ambassador}}', 'commitment', $this->string()->after('content'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%campaign_ambassador}}', 'commitment');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220210_214519_add_commitment_field_to_campaign_ambassador_table cannot be reverted.\n";

        return false;
    }
    */
}
