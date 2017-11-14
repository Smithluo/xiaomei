<?php

use yii\db\Migration;

class m170717_083702_create_o_analysis_sku extends Migration
{
    private $tableName = 'o_analysis_sku';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey()->notNull()->unsigned()->comment('ID'),
                'goods_id' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('商品id'),
                'date' => $this->date()->notNull()->defaultValue('0000-00-00')->comment('日期')
            ],
            $tableOptions
        );
        $this->createIndex('goods_id', $this->tableName, 'goods_id');
        $this->createIndex('date', $this->tableName, 'date');
    }

    public function safeDown()
    {
        echo "m170717_083702_create_o_analysis_sku cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170717_083702_create_o_analysis_sku cannot be reverted.\n";

        return false;
    }
    */
}
