<?php

use yii\db\Migration;

class m170505_012125_create_arrival_reminder extends Migration
{
    const TABLE =  'o_arrival_reminder';

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            self::TABLE,
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('user_id'),
                'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('goods_id'),
                'add_time' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('添加时间'),
                'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
            ],
            $tableOptions
        );
        $this->createIndex('user_id', self::TABLE, ['user_id', 'goods_id']);

    }

    public function safeDown()
    {
        $this->dropTable(self::TABLE);
    }
}
