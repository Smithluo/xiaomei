<?php

use yii\db\Migration;

class m170119_022304_create_o_servicer_user_region extends Migration
{
    public function safeUp()
    {
        $tableName = 'o_user_region';
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $tableName,
            [
                'user_id' => $this->integer()->notNull()->defaultValue(0)->comment('用户id'),
                'region_id' => $this->integer()->notNull()->defaultValue(0)->comment('区域id'),
                'PRIMARY KEY (user_id, region_id)',
            ],
            $tableOptions
        );

        $this->createIndex('user_id', $tableName, 'user_id');
    }

    public function safeDown()
    {
        $tableName = 'o_user_region';
        $this->dropIndex('user_id', $tableName);
        $this->dropTable($tableName);
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
