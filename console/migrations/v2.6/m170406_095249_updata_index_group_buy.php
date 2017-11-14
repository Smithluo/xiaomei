<?php

use yii\db\Migration;

class m170406_095249_updata_index_group_buy extends Migration
{
    private $tableName = 'o_index_group_buy';
    public function up()
    {
        $this->addColumn($this->tableName,'title','VARCHAR(20) NOT NULL DEFAULT " " COMMENT "标题"');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'title');
    }


}
