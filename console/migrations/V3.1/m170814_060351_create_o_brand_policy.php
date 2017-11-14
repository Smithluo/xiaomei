<?php

use yii\db\Migration;

class m170814_060351_create_o_brand_policy extends Migration
{
    private $tableName = 'o_brand_policy';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'brand_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('品牌id'),
                'policy_content' => $this->string(255)->notNull()->defaultValue('')->comment('商品'),
                'policy_link' => $this->string(255)->notNull()->defaultValue('')->comment('链接'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(1000)->comment('排序值'),
                'status' => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('状态'),
            ],
            $tableOptions
        );

        $this->createIndex('brand_id', $this->tableName, 'brand_id');
        $this->createIndex('status', $this->tableName, 'status');
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
        echo "m170814_060351_create_o_brand_policy cannot be reverted.\n";

        return false;
    }
    */
}
