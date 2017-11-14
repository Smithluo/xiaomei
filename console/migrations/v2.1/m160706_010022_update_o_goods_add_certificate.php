<?php

use yii\db\Migration;

class m160706_010022_update_o_goods_add_certificate extends Migration
{
    public function up()
    {
        $this->addColumn('o_goods', 'certificate', 'VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT"" COMMENT"证件号"');
    }

    public function down()
    {
        echo "m160706_010022_update_o_goods_add_certificate cannot be reverted.\n";

        return false;
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
