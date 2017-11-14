<?php

use yii\db\Migration;

class m170122_114315_create_o_sms_ip extends Migration
{
    public function safeUp()
    {
        $tableName = 'o_sms_ip';
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $tableName,
            [
                'ip' => $this->string(46)->notNull()->defaultValue(0)->comment('用户IP'),
                'count' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('访问次数'),
                'PRIMARY KEY (ip)',
            ],
            $tableOptions
        );

        $this->createIndex('ip', $tableName, 'ip');
    }

    public function safeDown()
    {
        $this->dropTable('o_sms_ip');
        return true;
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
