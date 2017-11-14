<?php

use yii\db\Migration;
use common\models\OrderGoods;

class m170113_094441_order_goods_add_pay_price extends Migration
{
    public function up()
    {
        $this->addColumn(
            OrderGoods::tableName(),
            'pay_price',
            " DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '实际结算价' "
        );
    }

    public function down()
    {
        $this->dropColumn(OrderGoods::tableName(), 'pay_price');
    }
}
