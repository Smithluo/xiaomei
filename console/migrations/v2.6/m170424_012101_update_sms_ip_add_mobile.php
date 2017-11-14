<?php

use yii\db\Migration;

class m170424_012101_update_sms_ip_add_mobile extends Migration
{
    private $tableName = 'o_sms_ip';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'mobile', 'VARCHAR(15) NOT NULL DEFAULT "" COMMENT "手机号码"');
        $this->createIndex('mobile', $this->tableName, 'mobile');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'mobile');
        return true;
    }

}
