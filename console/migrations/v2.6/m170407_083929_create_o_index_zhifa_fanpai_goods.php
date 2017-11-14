<?php

use yii\db\Migration;

class m170407_083929_create_o_index_zhifa_fanpai_goods extends Migration
{
    private $tableName = 'o_index_zhifa_fanpai';
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'goods_id' => $this->integer(10)->notNull()->defaultValue(0)->comment('商品ID'),
                'sort_order' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('排序值'),
            ],
            $tableOptions
        );
        $this->createIndex('goods_id', $this->tableName, 'goods_id');
        $this->createIndex('sort_order', $this->tableName, 'sort_order');
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
