<?php

use yii\db\Migration;

class m170612_063411_update_o_users_add_recommend_id extends Migration
{
    private $tableName = 'o_users';

    public function up()
    {
        $this->addColumn($this->tableName, 'recommend_id', 'MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "推荐者"');
        $this->createIndex('recommend_id', $this->tableName, 'recommend_id');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'recommend_id');
        return true;
    }

}
