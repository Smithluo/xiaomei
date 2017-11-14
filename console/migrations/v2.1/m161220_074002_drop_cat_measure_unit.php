<?php

use yii\db\Migration;

/**
 * 商品的单位只在商品中设置，删除商品分类中的商品单位——截止2016-12-20没有用到商品分类中的单位
 */
class m161220_074002_drop_cat_measure_unit extends Migration
{
    public function up()
    {
        $this->dropColumn('o_category', 'measure_unit');
    }

    public function down()
    {
        $this->addColumn('o_category', 'measure_unit', " varchar(15) NOT NULL DEFAULT '' ");
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
