<?php

use yii\db\Migration;

class m160907_064331_change_sort_order extends Migration
{
    /*public function up()
    {

    }

    public function down()
    {
        echo "m160907_064331_change_brand_goods_sort_order cannot be reverted.\n";

        return false;
    }*/


    // 修改排序规则 排序值大的排站前边，默认值设置在排序范围的中间; 添加最终排序值字段
    public function safeUp()
    {
        $this->alterColumn(
            'o_article',
            'sort_order',
            "TINYINT UNSIGNED NOT NULL DEFAULT '128' COMMENT '排序值（逆序，0～255）'"
        );

        $this->alterColumn(
            'o_article_cat',
            'sort_order',
            "TINYINT UNSIGNED NOT NULL DEFAULT '128' COMMENT '排序值（逆序，0～255）'"
        );

        $this->alterColumn(
            'o_attribute',
            'sort_order',
            "TINYINT UNSIGNED NOT NULL DEFAULT '128' COMMENT '排序值（逆序，0～255）'"
        );

        $this->alterColumn(
            'o_category',
            'sort_order',
            "TINYINT UNSIGNED NOT NULL DEFAULT '128' COMMENT '排序值（逆序，0～255）'"
        );

        $this->alterColumn(
            'o_favourable_activity',
            'sort_order',
            "TINYINT UNSIGNED NOT NULL DEFAULT '128' COMMENT '排序值（逆序，0～255）'"
        );

        $this->alterColumn(
            'o_shop_config',
            'sort_order',
            "TINYINT UNSIGNED NOT NULL DEFAULT '128' COMMENT '排序值（逆序，0～255）'"
        );

        $this->alterColumn(
            'o_template',
            'sort_order',
            "TINYINT UNSIGNED NOT NULL DEFAULT '128' COMMENT '排序值（逆序，0～255）'"
        );

        $this->alterColumn(
            'o_touch_article_cat',
            'sort_order',
            "TINYINT UNSIGNED NOT NULL DEFAULT '128' COMMENT '排序值（逆序，0～255）'"
        );

        $this->alterColumn(
            'o_brand',
            'sort_order',
            "TINYINT UNSIGNED NOT NULL DEFAULT '128' COMMENT '排序值（逆序，0～255）'"
        );

        $this->alterColumn(
            'o_goods',
            'sort_order',
            "SMALLINT UNSIGNED NOT NULL DEFAULT '30000' COMMENT '排序值（逆序 0～65535）'"
        );

        $this->addColumn(
            'o_goods',
            'complex_order',
            "INT UNSIGNED NULL DEFAULT '0' COMMENT '综合排序值' AFTER `shipping_code`"
        );
        $this->createIndex('complex_order', 'o_goods', 'complex_order');
    }

    public function safeDown()
    {
        echo "m160907_064331_change_brand_goods_sort_order cannot be reverted.\n";

        return false;
    }

}
