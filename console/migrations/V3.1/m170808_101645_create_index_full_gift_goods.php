<?php

use yii\db\Migration;

class m170808_101645_create_index_full_gift_goods extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable('o_index_full_gift_goods', [
            'id' => $this->primaryKey(),
            'title' => $this->string(30)->notNull()->defaultValue('')->comment('标题'),
            'sub_title' => $this->string(50)->notNull()->defaultValue('')->comment('描述'),
            'goods_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('商品ID'),
            'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('o_index_full_gift_goods');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170808_101645_create_index_full_gift_goods cannot be reverted.\n";

        return false;
    }
    */
}
