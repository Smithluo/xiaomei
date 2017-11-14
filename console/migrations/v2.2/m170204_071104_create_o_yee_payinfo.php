<?php

use yii\db\Migration;

class m170204_071104_create_o_yee_payinfo extends Migration
{
    public function safeUp()
    {
        $tableName = 'o_yee_payinfo';
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $tableName,
            [
                'id' => $this->primaryKey(),
                'order_sn' => $this->string(32)->notNull()->defaultValue('')->comment('订单编号'),
                'pay_log_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('支付日志'),
                'out_trade_no' => $this->string(32)->notNull()->defaultValue('')->comment('商户订单号'),
                'total_fee' => $this->money(10, 2)->defaultValue(0.0)->comment('支付金额'),
            ],
            $tableOptions
        );

        $this->createIndex('out_trade_no', $tableName, 'out_trade_no');
    }

    public function safeDown()
    {
        $tableName = 'o_yee_payinfo';
        $this->dropIndex('out_trade_no', $tableName);
        $this->dropTable($tableName);
        return true;
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
