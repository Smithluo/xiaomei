<?php

use yii\db\Migration;

/**
 * Handles the creation for table `yinlian_payinfo`.
 */
class m160815_035245_create_yinlian_payinfo extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('o_yinlian_payinfo', [
            'id' => $this->primaryKey(),
            'order_sn' => $this->string(32)->notNull()->defaultValue('')->comment('订单sn'),
            'pay_log_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('关联o_pay_log表的ID'),
            'out_trade_no' => $this->string(32)->notNull()->defaultValue('')->comment('商户订单ID'),
            'total_fee' => $this->money(10, 2)->notNull()->defaultValue(0)->comment('金额'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_yinlian_payinfo');
    }
}
