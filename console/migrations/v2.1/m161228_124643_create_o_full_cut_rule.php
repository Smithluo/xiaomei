<?php

use yii\db\Migration;

class m161228_124643_create_o_full_cut_rule extends Migration
{
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            'o_full_cut_rule',
            [
                'rule_id' => $this->primaryKey(),
                'rule_name' => $this->string(40)->notNull()->defaultValue('')->comment('规则名称'),
                'event_id' => $this->integer()->notNull()->defaultValue(0)->comment('活动ID'),
                'above' => $this->decimal(10, 2)->notNull()->defaultValue(0.01)->comment('满足金额'),
                'cut' => $this->decimal(10, 2)->notNull()->defaultValue(0.00)->comment('减免金额'),
                'status' => $this->integer(1)->notNull()->defaultValue(0)->comment('状态'),    //  0：无效， 1：生效
            ],
            $tableOptions
        );
    }

    public function down()
    {
        $this->dropTable('o_full_cut_rule');
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
