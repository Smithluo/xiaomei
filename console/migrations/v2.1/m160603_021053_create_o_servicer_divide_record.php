<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_servicer_divide_record`.
 */
class m160603_021053_create_o_servicer_divide_record extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_servicer_divide_record', [
            'id' => $this->primaryKey()->comment('分成流水id'),
            'order_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('订单ID'),
            'amount' => $this->money()->notNull()->defaultValue(0)->comment('商品总价'),
            'spec_strategy_id' => $this->text()->notNull()->comment('分成策略列表'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('零售店id'),
            'servicer_user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('服务商id'),
            'parent_servicer_user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('上级服务商id'),
            'divide_amount' => $this->money()->notNull()->defaultValue(0)->comment('分成金额'),
            'parent_divide_amount' => $this->money()->notNull()->defaultValue(0)->comment('上级服务商分成金额'),
            'child_record_id' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('下级服务商分成流水'),
            'money_in_record_id' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('用户钱包入账记录id'),
            'servicer_user_name' => $this->string(255)->notNull()->defaultValue('')->comment('业务员名称'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_servicer_divide_record');
    }
}
