<?php

use yii\db\Migration;

class m170518_023703_create_o_coupon_goods extends Migration
{
    private $tableName = 'o_coupon_topic_goods';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('商品'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            ],
            $tableOptions
        );
        $this->createIndex('goods_id', $this->tableName, 'goods_id');
        $this->createIndex('sort_order', $this->tableName, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
