<?php

use yii\db\Migration;

class m170410_061336_create_paihang extends Migration
{
    private $floorTableName = 'o_index_paihang_floor';
    private $goodsTableName = 'o_index_paihang_goods';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->floorTableName,
            [
                'id' => $this->primaryKey(),
                'title' => $this->string(20)->notNull()->defaultValue('')->comment('标题'),
                'description' => $this->text()->comment('描述文本'),
                'image' => $this->string(255)->notNull()->defaultValue('')->comment('图片'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
            ],
            $tableOptions
        );
        $this->createIndex('sort_order', $this->floorTableName, 'sort_order');

        $this->createTable(
            $this->goodsTableName,
            [
                'id' => $this->primaryKey(),
                'floor_id' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('楼层ID'),
                'title' => $this->string(60)->notNull()->defaultValue('')->comment('处于前3时的标题'),
                'description' => $this->text()->comment('处于前3时的描述文本'),
                'goods_id' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('商品ID'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
            ],
            $tableOptions
        );
        $this->createIndex('floor_id', $this->goodsTableName, 'floor_id');
        $this->createIndex('goods_id', $this->goodsTableName, 'goods_id');
        $this->createIndex('sort_order', $this->goodsTableName, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->floorTableName);
        $this->dropTable($this->goodsTableName);
        return true;
    }

}
