<?php

use yii\db\Migration;

class m170410_023217_update_season_goods_add_type extends Migration
{

    private  $tableName = 'o_season_goods';
    public function up()
    {
        $this->addColumn($this->tableName, 'type', 'SMALLINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT "类型"');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'type');
    }


}
