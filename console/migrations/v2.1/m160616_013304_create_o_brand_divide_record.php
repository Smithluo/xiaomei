<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_brand_divide_record`.
 */
class m160616_013304_create_o_brand_divide_record extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_brand_divide_record', [
            'id' => $this->primaryKey()->comment('分成流水ID'),
            'order_id' => $this->integer(8)->unsigned()->notNull()->comment('订单ID'),
            'brand_id' => $this->integer()->notNull()->comment('品牌ID'),
            'goods_amount' => $this->money()->notNull()->defaultValue(0)->comment('商品总价'),
            'shipping_fee' => $this->money()->notNull()->defaultValue(0)->comment('运费价格'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('零售店ID'),
            'divide_amount' => $this->money()->notNull()->defaultValue(0)->comment('结算金额'),
            'cash_record_id' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('入账记录ID'),
            'created_at' => $this->dateTime()->notNull()->defaultValue('0000-00-00 00:00:00')->comment('记录产生时间'),
            'status' => $this->integer(4)->defaultValue(0)->comment('提取状态'), //  0 未提取 2已提取
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_brand_divide_record');

    }
}