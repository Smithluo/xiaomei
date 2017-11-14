<?php

use yii\db\Migration;

class m160901_015618_update_o_tags_add_mCode extends Migration
{
    public function up()
    {
        $this->addColumn('o_tags', 'mCode', 'TEXT COMMENT "m站的code"');
    }

    public function down()
    {
        echo "m160901_015618_update_o_tags_add_mCode cannot be reverted.\n";

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
