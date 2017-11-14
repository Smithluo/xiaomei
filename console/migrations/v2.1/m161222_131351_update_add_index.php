<?php

use yii\db\Migration;

class m161222_131351_update_add_index extends Migration
{
    public function safeUp()
    {
        $this->createIndex('openid', 'o_users', 'openid');
        $this->createIndex('qq_open_id', 'o_users', 'qq_open_id');
        $this->createIndex('unionid', 'o_users', 'unionid');
        $this->createIndex('wx_pc_openid', 'o_users', 'wx_pc_openid');
        $this->createIndex('province', 'o_users', 'province');
        $this->createIndex('city', 'o_users', 'city');

        $this->createIndex('is_open', 'o_article', 'is_open');
        $this->createIndex('is_open', 'o_touch_article', 'is_open');
    }

    public function safeDown()
    {
        $this->dropIndex('openid', 'o_users');
        $this->dropIndex('qq_open_id', 'o_users');
        $this->dropIndex('unionid', 'o_users');
        $this->dropIndex('wx_pc_openid', 'o_users');
        $this->dropIndex('province', 'o_users');
        $this->dropIndex('city', 'o_users');

        $this->dropIndex('is_open', 'o_article');
        $this->dropIndex('is_open', 'o_touch_article');
        return true;
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
