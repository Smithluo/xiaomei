<?php

use yii\db\Migration;

class m170413_024045_create_user_extension extends Migration
{
    private $tableName = 'o_user_extension';



    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('user_id'),
                'store_number' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('店铺数量'),
                'month_sale_count' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('月营业额'),
                'imports_per' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('进口品占比'),
                'duty' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('职务'),
                'qq' => $this->string(10)->comment('qq号'),
                'birthday' => $this->timestamp()->defaultValue(0)->comment('生日'),
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
