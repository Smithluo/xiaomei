<?php

use yii\db\Migration;

class m170405_115156_update_guide_goods extends Migration
{
    private  $tableName = 'o_guide_goods';
    public function up()
    {
        $this->addColumn($this->tableName, 'sort_order', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 1 COMMENT "排序值"');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'sort_order');
    }

}
