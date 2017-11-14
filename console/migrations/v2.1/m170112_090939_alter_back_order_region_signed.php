<?php

use yii\db\Migration;
use common\models\BackOrder;

class m170112_090939_alter_back_order_region_signed extends Migration
{
    public function safeUp()
    {
        $tbName = BackOrder::tableName();
        $this->alterColumn($tbName, 'country', " SMALLINT(5) NOT NULL DEFAULT '0' ");
        $this->alterColumn($tbName, 'province', " SMALLINT(5) NOT NULL DEFAULT '0' ");
        $this->alterColumn($tbName, 'city', " SMALLINT(5) NOT NULL DEFAULT '0' ");
        $this->alterColumn($tbName, 'district', " SMALLINT(5) NOT NULL DEFAULT '0' ");
    }

    public function safeDown()
    {
        $tbName = BackOrder::tableName();
        $this->alterColumn($tbName, 'country', " SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ");
        $this->alterColumn($tbName, 'province', " SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ");
        $this->alterColumn($tbName, 'city', " SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ");
        $this->alterColumn($tbName, 'district', " SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ");
    }
}
