<?php

use yii\db\Migration;

class m170408_083611_update_o_season_goods_add_is_show extends Migration
{

    private  $tableName = 'o_season_goods';
    public function up()
    {
        $this->addColumn($this->tableName, 'is_show', 'SMALLINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "是否显示"');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'is_show');
    }

}
