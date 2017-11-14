<?php

use yii\db\Migration;

use common\models\OrderGoods;

class m170121_021417_alter_order_goods_change_goods_attr_null extends Migration
{
    public function up()
    {
        $this->alterColumn(
            OrderGoods::tableName(),
            'goods_attr',
            " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'SKU属性列表' "
        );
    }

    public function down()
    {
        $this->alterColumn(
            OrderGoods::tableName(),
            'goods_attr',
            " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'SKU属性列表' "
        );
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
