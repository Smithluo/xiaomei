<?php

use yii\db\Migration;

class m170407_080500_update_o_users_change_check_note_to_text extends Migration
{
    public function up()
    {
        $this->alterColumn('o_users', 'checked_note', 'TEXT COMMENT "审核意见"');
    }

    public function down()
    {
        echo "m170407_080500_update_o_users_change_check_note_to_text cannot be reverted.\n";

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
