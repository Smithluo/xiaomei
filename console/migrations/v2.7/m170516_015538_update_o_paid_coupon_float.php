<?php

use yii\db\Migration;

class m170516_015538_update_o_paid_coupon_float extends Migration
{
    public function up()
    {
        $this->alterColumn('o_paid_coupon', 'amount', 'DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT "满足金额"');
    }

    public function down()
    {
        $this->alterColumn('o_paid_coupon', 'amount', 'INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT "满足金额"');
        return true;
    }

}
