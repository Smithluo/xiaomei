<?php

use yii\db\Migration;

class m170405_084914_create_table_guide_type_and_guide_goods extends Migration
{
    private $guideTypeTable = 'o_guide_type';
    private $guideGoodsTable = 'o_guide_goods';
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->guideTypeTable,
            [
                'id' => $this->primaryKey(),
                'title' => $this->string(20)->notNull()->defaultValue('')->comment('标题'),
                'desc' => $this->string(20)->notNull()->defaultValue('')->comment('指南描述'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(100)->comment('排序值'),
            ],
            $tableOptions
        );
        $this->createTable(
            $this->guideGoodsTable,
            [
                'id' => $this->primaryKey(),
                'type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('类型'),
                'goods_id' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('商品id'),
            ],
            $tableOptions
        );
        $this->createIndex('sort_order', $this->guideTypeTable, 'sort_order');
    }

    public function down()
    {
        $this->dropTable($this->guideGoodsTable);
        $this->dropTable($this->guideTypeTable);
    }

}
