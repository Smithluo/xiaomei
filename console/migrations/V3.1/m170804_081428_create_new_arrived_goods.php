<?php

use yii\db\Migration;

class m170804_081428_create_new_arrived_goods extends Migration
{
    private $tableName = 'o_new_arrived_goods';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'goods_id' => $this->smallInteger(5)->unique()->unsigned()->notNull()->defaultValue(0)->comment('商品'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(1000)->comment('排序值'),
            ],
            $tableOptions
        );

        $this->createIndex('sort_order', $this->tableName, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170804_081428_create_new_arrived_goods cannot be reverted.\n";

        return false;
    }
    */
}
