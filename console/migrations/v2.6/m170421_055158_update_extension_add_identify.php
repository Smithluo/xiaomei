<?php

use yii\db\Migration;

class m170421_055158_update_extension_add_identify extends Migration
{

    private $tableName = 'o_user_extension';
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'identify', 'SMALLINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "是否验证"');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'identify');
    }
}
