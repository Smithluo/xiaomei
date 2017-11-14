<?php

use yii\db\Migration;

class m170408_074632_update_o_brand_add_is_hot extends Migration
{
    private $tableName = 'o_brand';
    public function up()
    {
        $this->addColumn($this->tableName, 'is_hot', 'SMALLINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT "是否热门"');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'is_hot');
    }

}
