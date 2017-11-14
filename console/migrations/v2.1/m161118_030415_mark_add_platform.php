<?php

use yii\db\Migration;

class m161118_030415_mark_add_platform extends Migration
{
    public function up()
    {
        $this->db = Yii::$app->dboa;
        $this->addColumn('oa_mark', 'plat_form'," VARCHAR(10) NOT NULL COMMENT '平台' AFTER `user_id` ");
        $this->createIndex('plat_form', 'oa_mark', 'plat_form');
    }

    public function down()
    {
        echo "m161118_030415_mark_add_platform cannot be reverted.\n";

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
