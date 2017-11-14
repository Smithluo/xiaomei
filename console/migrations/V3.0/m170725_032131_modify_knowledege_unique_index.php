<?php

use yii\db\Migration;

class m170725_032131_modify_knowledege_unique_index extends Migration
{
    public $tbName = 'o_knowledge_show_brand';

    public function safeUp()
    {
        $this->dropIndex('brand_id', $this->tbName);
        $this->createIndex('platform_brand_id', $this->tbName, ['brand_id', 'platform'], true);
    }

    public function safeDown()
    {
        $this->dropIndex('platform_brand_id', $this->tbName);
        $this->createIndex('brand_id', $this->tbName, 'brand_id', true);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170725_032131_modify_knowledege_unique_index cannot be reverted.\n";

        return false;
    }
    */
}
