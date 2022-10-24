<?php

use yii\db\Migration;

/**
 * Class m201205_134618_add_field_to_campaign_ambassador_table
 */
class m201205_134618_add_field_to_campaign_ambassador_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%campaign_ambassador}}', 'address', $this->string()->after('image'));
        $this->addColumn('{{%campaign_ambassador}}', 'tshirt_size', $this->string(4)->after('address'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%campaign_ambassador}}', 'address');
        $this->dropColumn('{{%campaign_ambassador}}', 'tshirt_size');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201205_134618_add_field_to_campaign_ambassador_table cannot be reverted.\n";

        return false;
    }
    */
}
