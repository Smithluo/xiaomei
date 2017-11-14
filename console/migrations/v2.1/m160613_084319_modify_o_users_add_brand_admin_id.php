<?php

use yii\db\Migration;

/**
 * o_users表添加 品牌商ID（品牌商扩展信息表的ID）
 * Class m160613_084319_modify_o_users_add_brand_user_id
 */
class m160613_084319_modify_o_users_add_brand_admin_id extends Migration
{
    public function up()
    {
        $this->addColumn('o_users', 'brand_admin_id', "INT NULL DEFAULT '0' COMMENT '品牌商ID' AFTER `bank_info_id`");
    }

    public function down()
    {
        echo "m160613_084319_modify_o_users_add_brand_admin_id cannot be reverted.\n";

        return false;
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
