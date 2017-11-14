<?php

use yii\db\Migration;

class m170406_103010_update_goods_activity extends Migration
{
    private $tableName = 'o_goods_activity';
    public function up()
    {
        $this->addColumn($this->tableName, 'note', 'VARCHAR(20) COMMENT "备注"');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'note');
    }

}
