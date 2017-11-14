<?php

use yii\db\Migration;
use common\models\ShippingArea;

/**
 * Class m170122_021247_shipping_area_add_weight
 * 运费模板添加重量配置
 */
class m170122_021247_shipping_area_add_weight extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            ShippingArea::tableName(),
            'overweight',
            " DECIMAL(10,3) NOT NULL DEFAULT '0.000' COMMENT '重量范围(Kg)' "
        );
        $this->createIndex('overweight', ShippingArea::tableName(), 'overweight');
    }

    public function safeDown()
    {
        $this->dropIndex('overweight', ShippingArea::tableName());
        $this->dropColumn(ShippingArea::tableName(), 'overweight');
    }
}
