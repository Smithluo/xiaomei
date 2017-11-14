<?php

use yii\db\Migration;

class m170510_112257_create_register_done_goods extends Migration
{

    private $tableName = 'o_register_done_goods';
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('商品id'),
                'is_show' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否显示'),
            ],
            $tableOptions
        );
        $this->createIndex('goods_id', $this->tableName, 'goods_id');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
