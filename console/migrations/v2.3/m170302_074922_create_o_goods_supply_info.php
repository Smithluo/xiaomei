<?php

use yii\db\Migration;

class m170302_074922_create_o_goods_supply_info extends Migration
{
    private $tableName = 'o_goods_supply_info';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $this->tableName,
            [
                'goods_id' => $this->integer(8)->notNull()->defaultValue(0)->comment('商品ID'),
                'supply_price' => $this->decimal(10, 2)->unsigned()->notNull()->defaultValue(0)->comment('采购价'),
                'PRIMARY KEY (goods_id)',
            ],
            $tableOptions
        );
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
        return true;
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
