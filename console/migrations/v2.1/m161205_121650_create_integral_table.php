<?php

use yii\db\Migration;

/**
 * Handles the creation for table `integral_table`.
 */
class m161205_121650_create_integral_table extends Migration
{
    /**
     * 积分流水表
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_integral', [
            'id' => $this->primaryKey(),
            'integral' => $this->integer()->notNull()->comment('积分出入账'),
            'user_id' => $this->integer()->unsigned()->notNull()->comment('用户ID'),
            'pay_code' => $this->string(20)->notNull()->defaultValue('backend')->comment('支付方式'),
            'out_trade_no' => $this->string(32)->notNull()->comment('第三方支付流水号'),    //  批量支付只记录一条
            'note' => $this->string(256)->notNull()->comment('订单号'), //  管理员操作时记录 管理员名称、IP、动作，默认订单号列表
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),   //  格林威治时间戳
            'updated_at' => $this->integer()->unsigned()->notNull()->comment('更新时间'),   //  格林威治时间戳

            //  0 冻结(下单送积分，冻结，订单完成后解冻，更新用户可用余额)
            'status' => $this->integer(1)->notNull()->defaultValue(0)->comment('积分状态'),
        ], $tableOptions);

        $this->createIndex('user_id', 'o_integral', 'user_id');
        $this->createIndex('updated_at', 'o_integral', 'updated_at');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('o_integral');
    }
}
