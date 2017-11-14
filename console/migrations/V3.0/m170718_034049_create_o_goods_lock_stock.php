<?php

use yii\db\Migration;

class m170718_034049_create_o_goods_lock_stock extends Migration
{
    private $tableName = 'o_goods_lock_stock';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('商品'),
                'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('操作者'),
                'enable' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('是否可用'),
                'lock_num' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('锁定数量'),
                'lock_time' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('操作锁定的时间'),
                'expired_time' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('自动解锁时间'),
                'note' => $this->text()->comment('锁定备注'),
            ],
            $tableOptions
        );

        $this->createIndex('goods_id', $this->tableName, 'goods_id');
        $this->createIndex('user_id', $this->tableName, 'user_id');
        $this->createIndex('enable', $this->tableName, 'enable');
        $this->createIndex('lock_time', $this->tableName, 'lock_time');
        $this->createIndex('expired_time', $this->tableName, 'expired_time');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170718_034049_create_o_goods_lock_stock cannot be reverted.\n";

        return false;
    }
    */
}
