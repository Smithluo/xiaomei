<?php

use yii\db\Migration;

class m160603_060245_update_o_users extends Migration
{
    /**
     * 用于标记账号为品牌商
     */
    public function up()
    {
        $this->addColumn('o_users', 'brand_id_list', 'VARCHAR(255) NULL DEFAULT "0" COMMENT "品牌商旗下的品牌ID" AFTER servicer_super_id');
    }

    public function down()
    {
        echo "m160603_060245_update_o_users cannot be reverted.\n";
        return false;
    }

}
