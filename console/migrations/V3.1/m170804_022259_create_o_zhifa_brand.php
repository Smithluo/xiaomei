<?php

use yii\db\Migration;

class m170804_022259_create_o_zhifa_brand extends Migration
{
    private $tableZhifaBrand = 'o_zhifa_brand';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableZhifaBrand,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'brand_id' => $this->smallInteger(5)->unique()->unsigned()->notNull()->defaultValue(0)->comment('品牌'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(1000)->comment('排序值'),
            ],
            $tableOptions
        );

        $this->createIndex('sort_order', $this->tableZhifaBrand, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableZhifaBrand);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170804_022259_create_o_zhifa_brand cannot be reverted.\n";

        return false;
    }
    */
}
