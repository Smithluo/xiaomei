<?php

use yii\db\Migration;

class m170325_090200_update_o_fashion_goods_and_o_season_goods extends Migration
{
    private $tableSeasonGoods ='o_season_goods';
    private $tableFashionGoods ='o_fashion_goods';



    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn($this->tableFashionGoods, 'name','VARCHAR(20) NOT NULL DEFAULT "" COMMENT "标题"');
        $this->addColumn($this->tableFashionGoods, 'desc','VARCHAR(64) NOT NULL DEFAULT "" COMMENT "描述"');

        $this->addColumn($this->tableSeasonGoods, 'name','VARCHAR(20) NOT NULL DEFAULT "" COMMENT "标题"');
        $this->addColumn($this->tableSeasonGoods, 'desc','VARCHAR(64) NOT NULL DEFAULT "" COMMENT "描述"');

    }

    public function safeDown()
    {
        $this->dropColumn($this->tableFashionGoods,'name');
        $this->dropColumn($this->tableFashionGoods,'desc');

        $this->dropColumn($this->tableSeasonGoods,'name');
        $this->dropColumn($this->tableSeasonGoods,'desc');

    }

}
