<?php

use yii\db\Migration;

class m170810_072218_update_goods_collection_add_keywords extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_goods_collection', 'keywords', 'VARCHAR(60) NOT NULL DEFAULT "" COMMENT "关键词"');
    }

    public function safeDown()
    {
        $this->dropColumn('o_goods_collection', 'keywords');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170810_072218_update_goods_collection_add_keywords cannot be reverted.\n";

        return false;
    }
    */
}
