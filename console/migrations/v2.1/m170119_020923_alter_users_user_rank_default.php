<?php

use yii\db\Migration;
use common\models\Users;

class m170119_020923_alter_users_user_rank_default extends Migration
{
    public function up()
    {
        $this->alterColumn(Users::tableName(), 'user_rank', " TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' ");
    }

    public function down()
    {
        $this->alterColumn(Users::tableName(), 'user_rank', " TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' ");
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
