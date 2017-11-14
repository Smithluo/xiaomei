<?php

use yii\db\Migration;

class m170801_072821_create_o_goods_colection extends Migration
{
    private $tableGoodsCollection = 'o_goods_collection';
    private $tableGoodsCollectionItem = 'o_goods_collection_item';
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableGoodsCollection,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'title' => $this->string(60)->notNull()->defaultValue('')->comment('标题'),
                'desc' => $this->text()->comment('摘要'),
                'create_time' => $this->dateTime()->notNull()->defaultValue(0)->comment('创建时间'),
                'click_init' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('初始点击次数'),
                'click' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('点击次数'),
                'color' => $this->string(10)->unsigned()->notNull()->defaultValue('069fc8')->comment('颜色值'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(1000)->comment('排序值'),
                'is_show' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('是否显示'),
            ],
            $tableOptions
        );

        $this->createIndex('sort_order', $this->tableGoodsCollection, 'sort_order');
        $this->createIndex('create_time', $this->tableGoodsCollection, 'create_time');
        $this->createIndex('is_show', $this->tableGoodsCollection, 'is_show');

        $this->createTable(
            $this->tableGoodsCollectionItem,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'coll_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('专辑'),
                'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('商品'),
                'sort_order' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
            ],
            $tableOptions
        );

        $this->createIndex('coll_id', $this->tableGoodsCollectionItem, 'coll_id');
        $this->createIndex('goods_id', $this->tableGoodsCollectionItem, 'goods_id');
        $this->createIndex('sort_order', $this->tableGoodsCollectionItem, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableGoodsCollection);
        $this->dropTable($this->tableGoodsCollectionItem);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170801_072821_create_o_goods_colection cannot be reverted.\n";

        return false;
    }
    */
}
