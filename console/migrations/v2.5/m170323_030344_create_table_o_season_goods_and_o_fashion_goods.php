<?php

use yii\db\Migration;

class m170323_030344_create_table_o_season_goods_and_o_fashion_goods extends Migration
{
    private $tableNameSeasonGoods = 'o_season_goods';
    private $tableNameFashionGoods = 'o_fashion_goods';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $this->tableNameSeasonGoods,
            [
                'id' => $this->primaryKey(),
                'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('商品ID'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(100)->comment('排序值'),
            ],
            $tableOptions
        );

        $this->createTable(
            $this->tableNameFashionGoods,
            [
                'id' => $this->primaryKey(),
                'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('商品ID'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(100)->comment('排序值'),
            ],
            $tableOptions
        );

        $this->createIndex('goods_id', $this->tableNameSeasonGoods, 'goods_id');
        $this->createIndex('sort_order', $this->tableNameSeasonGoods, 'sort_order');
        $this->createIndex('goods_id', $this->tableNameFashionGoods, 'goods_id');
        $this->createIndex('sort_order', $this->tableNameFashionGoods, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableNameSeasonGoods);
        $this->dropTable($this->tableNameFashionGoods);
    }

}
