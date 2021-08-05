<?php

use yii\db\Migration;

/**
 * Class m210805_133854_add_paypal_order_id_to_orders_table
 */
class m210805_133854_add_paypal_order_id_to_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'paypal_order_id', $this->string(255)->after('transaction_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'paypal_order_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210805_133854_add_paypal_order_id_to_orders_table cannot be reverted.\n";

        return false;
    }
    */
}
