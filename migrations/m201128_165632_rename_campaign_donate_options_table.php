<?php

use yii\db\Migration;

/**
 * Class m201129_152928_rename_campaign_donate_options_table
 */
class m201128_165632_rename_campaign_donate_options_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_campaign_donate_option_campaign_id_campaign_id', '{{%campaign_donate_option}}');
        $this->renameTable('{{%campaign_donate_option}}', '{{%campaign_donation_option}}');
        $this->addForeignKey('fk_campaign_donation_option_campaign_id_campaign_id', '{{%campaign_donation_option}}', 'campaign_id', '{{%campaign}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_campaign_donation_option_campaign_id_campaign_id', '{{%campaign_donation_option}}');
        $this->renameTable('{{%campaign_donation_option}}', '{{%campaign_donate_option}}');
        $this->addForeignKey('fk_campaign_donate_option_campaign_id_campaign_id', '{{%campaign_donate_option}}', 'campaign_id', '{{%campaign}}', 'id', 'CASCADE', 'CASCADE');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201129_152928_rename_campaign_donate_options_table cannot be reverted.\n";

        return false;
    }
    */
}
