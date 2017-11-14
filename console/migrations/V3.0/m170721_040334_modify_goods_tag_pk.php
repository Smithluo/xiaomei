<?php

use yii\db\Migration;

class m170721_040334_modify_goods_tag_pk extends Migration
{
    private $tableName = 'o_goods_tag';

    public function safeUp()
    {
        $this->addPrimaryKey('goods_tag_pk', $this->tableName, ['goods_id', 'tag_id']);
    }

    public function safeDown()
    {
        echo "m170721_040334_modify_goods_tag_pk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170721_040334_modify_goods_tag_pk cannot be reverted.\n";

        return false;
    }
    */
}
