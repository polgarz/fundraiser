<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%donation}}`.
 */
class m201212_224058_add_address_columns_to_donation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%donation}}', 'zip', $this->integer()->after('message'));
        $this->addColumn('{{%donation}}', 'city', $this->string()->after('zip'));
        $this->addColumn('{{%donation}}', 'street', $this->string()->after('city'));
        $this->addColumn('{{%donation}}', 'note', $this->string()->after('street'));

        $this->addColumn('{{%campaign}}', 'address_required', $this->boolean()->after('custom_donation_available')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%donation}}', 'zip');
        $this->dropColumn('{{%donation}}', 'city');
        $this->dropColumn('{{%donation}}', 'street');
        $this->dropColumn('{{%donation}}', 'note');
    }
}
