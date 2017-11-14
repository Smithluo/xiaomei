<?php

use yii\db\Migration;

/**
 * 用户积分的可用余额 现在用户信息表中
 * 如果用户积分流水有变更(create、update)，则强置int_balacne为0
 * 当需要用到积分余额时再做计算，节省开销
 * Class m161208_023739_users_add_int_balance
 */
class m161208_023739_users_add_int_balance extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_users', 'int_balance', " INT NOT NULL DEFAULT '0' COMMENT '积分可用余额' ");
        $this->createIndex('int_balance', 'o_users', 'int_balance');
    }

    public function safeDown()
    {
        $this->dropColumn('o_users', 'int_balance');
    }

}
