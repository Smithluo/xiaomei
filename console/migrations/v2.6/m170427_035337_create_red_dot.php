<?php

use yii\db\Migration;

class m170427_035337_create_red_dot extends Migration
{

    private $tableName = 'o_red_dot';
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('user_id'),
            ],
            $tableOptions
        );
        $this->createIndex('user_id', $this->tableName, 'user_id');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
