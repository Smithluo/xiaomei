<?php

use yii\db\Migration;

//  修改 (生成订单的事务)用到的【表引擎】 为InnoDB
class m170512_084330_change_table_engine_to_support_transaction extends Migration
{
    public function safeUp()
    {
        $this->execute(" ALTER TABLE o_order_info engine = InnoDB; ");
        $this->execute(" ALTER TABLE o_order_goods engine = InnoDB ");
        $this->execute(" ALTER TABLE o_users engine = InnoDB ");
        $this->execute(" ALTER TABLE o_pay_log engine = InnoDB ");
        $this->execute(" ALTER TABLE o_cart engine = InnoDB ");
    }

    public function safeDown()
    {
        $this->execute(" ALTER TABLE o_order_info engine = MyISAM ");
        $this->execute(" ALTER TABLE o_order_goods engine = MyISAM ");
        $this->execute(" ALTER TABLE o_users engine = MyISAM ");
        $this->execute(" ALTER TABLE o_pay_log engine = MyISAM ");
        $this->execute(" ALTER TABLE o_cart engine = MyISAM ");
    }

}
