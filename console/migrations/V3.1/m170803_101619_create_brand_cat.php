<?php

use yii\db\Migration;

class m170803_101619_create_brand_cat extends Migration
{
    private $tableName = 'o_brand_cat';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'brand_id' => $this->smallInteger(5)->notNull()->comment('品牌ID'),
                'cat_id' => $this->smallInteger(5)->notNull()->comment('品类ID'),
            ],
            $tableOptions
        );
        $this->createIndex('brand_id',$this->tableName, 'brand_id');
        $this->createIndex('cat_id',$this->tableName, 'cat_id');
        $this->createIndex('brand_cat', $this->tableName, ['brand_id', 'cat_id'], true);
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
        echo "m170803_101619_create_brand_cat cannot be reverted.\n";

        return false;
    }
    */
}
