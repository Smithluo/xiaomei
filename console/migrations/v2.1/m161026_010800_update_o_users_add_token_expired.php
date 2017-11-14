<?php

use yii\db\Migration;

class m161026_010800_update_o_users_add_token_expired extends Migration
{
    public function up()
    {
        $this->addColumn('o_users', 'token_expired', 'DATETIME COMMENT "token失效时间"');
    }

    public function down()
    {
        $this->dropColumn('o_users', 'token_expired');
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
