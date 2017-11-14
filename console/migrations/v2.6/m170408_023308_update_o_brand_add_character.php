<?php

use yii\db\Migration;

class m170408_023308_update_o_brand_add_character extends Migration
{
    private $tableName = 'o_brand';
    public function up()
    {
        $this->addColumn($this->tableName, 'character', 'CHAR(1) NOT NULL DEFAULT " " COMMENT "首字母"');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'character');
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
