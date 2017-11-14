<?php

use yii\db\Migration;

class m170627_075442_create_o_order_modify_action extends Migration
{
    private $tableName = 'o_order_modify_action';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'action_user' => $this->string('255')->notNull()->defaultValue('')->comment('操作者'),
                'group_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('总单'),
                'order_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('订单'),
                'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('用户'),
                'consignee' => $this->string(60)->notNull()->defaultValue('')->comment('收件人'),
                'mobile' => $this->string(60)->notNull()->defaultValue('')->comment('收件人手机号码'),
                'province' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('省'),
                'city' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('市'),
                'district' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('区'),
                'address' => $this->string(255)->notNull()->defaultValue('')->comment('地址'),
                'time' => $this->dateTime(),
            ],
            $tableOptions
        );

        $this->createIndex('user_id', $this->tableName, 'user_id');
        $this->createIndex('group_id', $this->tableName, 'group_id');
        $this->createIndex('order_id', $this->tableName, 'order_id');
        $this->createIndex('time', $this->tableName, 'time');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
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
