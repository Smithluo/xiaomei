<?php

use yii\db\Migration;

/**
 * 删除没有手机号的用户， 设置mobile_phone 为唯一性索引
 * Class m170905_113057_modify_users_mobile_phone_unique
 */
class m170905_113057_modify_users_mobile_phone_unique extends Migration
{
    public function safeUp()
    {
        $this->db->createCommand(" DELETE FROM `o_users` WHERE (mobile_phone = '' OR mobile_phone IS NULL) ")->execute();
        $this->dropIndex('mobile_phone', 'o_users');
        $this->createIndex('mobile_phone', 'o_users', 'mobile_phone', true);
    }

    public function safeDown()
    {
        $this->dropIndex('mobile_phone', 'o_users');
        $this->createIndex('mobile_phone', 'o_users', 'mobile_phone');
    }
}
