<?php

use yii\db\Migration;

class m170717_083655_create_o_analysis_brand extends Migration
{
    private $tableName = 'o_analysis_brand';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey()->notNull()->unsigned()->comment('ID'),
                'brand_id' => $this->smallInteger(5)->notNull()->unsigned()->defaultValue(0)->comment('品牌id'),
                'date' => $this->date()->notNull()->defaultValue('0000-00-00')->comment('日期')
            ],
            $tableOptions
        );

        $this->createIndex('brand_id', $this->tableName, 'brand_id');
        $this->createIndex('date', $this->tableName, 'date');
    }

    public function safeDown()
    {
        echo "m170717_083655_create_o_analysis_brand cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170717_083655_create_o_analysis_brand cannot be reverted.\n";

        return false;
    }
    */
}
