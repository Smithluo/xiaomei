<?php

use yii\db\Migration;
use common\models\OrderInfo;

class m170112_025522_alter_order_info_region_signed extends Migration
{
    public function safeUp()
    {
        $orderInfoTb = OrderInfo::tableName();
        $this->alterColumn($orderInfoTb, 'country', " SMALLINT(5) NOT NULL DEFAULT '0' ");
        $this->alterColumn($orderInfoTb, 'province', " SMALLINT(5) NOT NULL DEFAULT '0' ");
        $this->alterColumn($orderInfoTb, 'city', " SMALLINT(5) NOT NULL DEFAULT '0' ");
        $this->alterColumn($orderInfoTb, 'district', " SMALLINT(5) NOT NULL DEFAULT '0' ");
    }

    public function safeDown()
    {
        $orderInfoTb = OrderInfo::tableName();
        $this->alterColumn($orderInfoTb, 'country', " SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ");
        $this->alterColumn($orderInfoTb, 'province', " SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ");
        $this->alterColumn($orderInfoTb, 'city', " SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ");
        $this->alterColumn($orderInfoTb, 'district', " SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ");
    }
}
