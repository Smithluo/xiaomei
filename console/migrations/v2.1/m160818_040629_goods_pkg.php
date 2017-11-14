<?php

use yii\db\Migration;

class m160818_040629_goods_pkg extends Migration
{
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_goods_pkg', [
            'pkg_id' => $this->primaryKey()->comment('商品包ID'),
            'pkg_name' => $this->string(80)->notNull()->comment('商品包名称'),
            'allow_goods_list' => $this->text()->comment('商品包支持的范围'),
            //  除这个列表之外的所有商品都可以参与活动
            'deny_goods_list' => $this->text()->comment('商品包不支持的范围'),
            'updated_at' => $this->integer()->notNull()->comment('创建时间'),
        ], $tableOptions);
    }

    public function down()
    {
        /*echo "m160818_040629_goods_pkg cannot be reverted.\n";

        return false;*/
        $this->dropTable('o_goods_pkg');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
