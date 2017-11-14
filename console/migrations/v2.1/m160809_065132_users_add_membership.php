<?php

use yii\db\Migration;

class m160809_065132_users_add_membership extends Migration
{
    public function safeUp()
    {
        $table = 'o_users';
        $this->addColumn($table, 'shopfront_pic', " VARCHAR(255) NOT NULL DEFAULT '' COMMENT '店铺门头' AFTER `user_type`");
        $this->addColumn($table, 'biz_license_pic', " VARCHAR(255) NOT NULL DEFAULT '' COMMENT '营业执照' AFTER `shopfront_pic`");
        $this->addColumn($table, 'province', " SMALLINT NOT NULL DEFAULT '0' COMMENT '所在省份' AFTER `biz_license_pic`");
        $this->addColumn($table, 'city', " SMALLINT NOT NULL DEFAULT '0' COMMENT '所在城市' AFTER `province`");
    }

    public function safeDown()
    {
        echo "m160809_065132_users_add_membership cannot be reverted.\n";

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
