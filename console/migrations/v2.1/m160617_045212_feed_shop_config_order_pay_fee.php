<?php

use yii\db\Migration;

class m160617_045212_feed_shop_config_order_pay_fee extends Migration
{
    public function safeUp()
    {
        //  设置默认手续费
        $this->insert('o_shop_config', [
            'parent_id' => 1,
            'code' => 'order_pay_fee',
            'type' => 'text',
            'value' => '0.006',
        ]);
        //  设置提现最低额度
        $this->insert('o_shop_config', [
            'parent_id' => 1,
            'code' => 'withdraw_min',
            'type' => 'text',
            'value' => '1000',
        ]);
    }

    public function safeDown()
    {
        echo "m160617_045212_feed_shop_config_order_pay_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
