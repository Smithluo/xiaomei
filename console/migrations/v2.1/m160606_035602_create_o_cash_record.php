<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_cash_record`.
 */
class m160606_035602_create_o_cash_record extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_cash_record', [
            'id' => $this->primaryKey(),
            'cash' => $this->money()->notNull()->defaultValue(0)->comment('金额'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('用户id'),
            'note' => $this->string(255)->notNull()->defaultValue('')->comment('备注'),
            'pay_time' => $this->dateTime()->defaultValue(0)->comment('汇款时间'),
            'created_time' => $this->dateTime()->defaultValue(0)->comment('创建时间'),
            'status' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('状态'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_cash_record');
    }
}
