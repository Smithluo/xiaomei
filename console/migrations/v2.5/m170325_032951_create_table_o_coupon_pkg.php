<?php

use yii\db\Migration;

class m170325_032951_create_table_o_coupon_pkg extends Migration
{
    private $tableName = 'o_coupon_pkg';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $this->tableName,
            [
                'event_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('活动ID'),
                'enable' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否可以领取'),
                'PRIMARY KEY (event_id)',
            ],
            $tableOptions
        );

        $this->createIndex('enable', $this->tableName, 'enable');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
