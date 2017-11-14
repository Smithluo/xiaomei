<?php

use yii\db\Migration;

class m170105_060632_create_o_order_group extends Migration
{
    public function safeUp()
    {
        $tableName = 'o_order_group';
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $tableName,
            [
                'id' => $this->primaryKey(),
                'group_id' => $this->string(13)->notNull()->defaultValue('')->comment('组id'),
                'user_id' => $this->integer()->notNull()->defaultValue(0)->comment('用户ID'),
                'create_time' => $this->integer(10)->notNull()->defaultValue(0)->comment('创建时间戳'),
            ],
            $tableOptions
        );

        $this->createIndex('group_id', $tableName, 'group_id');
        $this->createIndex('user_id', $tableName, 'user_id');
        $this->createIndex('create_time', $tableName, 'create_time');
    }

    public function safeDown()
    {
        $tableName = 'o_order_group';
        $this->dropIndex('group_id', $tableName);
        $this->dropIndex('user_id', $tableName);
        $this->dropIndex('create_time', $tableName);
        $this->dropTable('o_order_group');
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
