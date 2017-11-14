<?php

use yii\db\Migration;

class m160607_065837_modify_order_info_add_brand_id extends Migration
{
    public $table_name = 'o_order_info';
    public function up()
    {
        $this->addColumn($this->table_name, 'brand_id', "INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '品牌' AFTER `mobile_order`");
        $this->createIndex('brand_id', $this->table_name, 'brand_id');
    }

    public function down()
    {
        $this->dropColumn($this->table_name, 'brand_id');
        echo "!Warning:m160607_065837_modify_order_info_add_brand_id cannot be down,But you can exec redo.\n";
//        return false;
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
