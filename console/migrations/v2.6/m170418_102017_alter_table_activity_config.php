<?php

use yii\db\Migration;

class m170418_102017_alter_table_activity_config extends Migration
{
    private $tableName = 'o_activity_config';

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->alterColumn($this->tableName, 'api', 'VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL  ');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'api');
    }

}
