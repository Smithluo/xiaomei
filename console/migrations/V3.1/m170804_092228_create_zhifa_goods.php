<?php

use yii\db\Migration;

class m170804_092228_create_zhifa_goods extends Migration
{
    private $tableName = 'o_zhifa_goods';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'type' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('类型'),
                'goods_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('商品'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(1000)->comment('排序值'),
            ],
            $tableOptions
        );

        $this->createIndex('goods_id', $this->tableName, 'goods_id');
        $this->createIndex('type', $this->tableName, 'type');
        $this->createIndex('sort_order', $this->tableName, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170804_092228_create_zhifa_goods cannot be reverted.\n";

        return false;
    }
    */
}
