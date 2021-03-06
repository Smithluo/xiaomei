<?php

use yii\db\Migration;

class m170907_101028_create_table_purchase_back_order_goods extends Migration
{
    public $tbName = 'o_purchase_back_order_goods';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB ';

        $this->createTable(
            $this->tbName,
            [
                'id'                => $this->primaryKey()->comment('ID'),
                'purchase_back_order_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('退货单号'),
                'goods_sn'          => $this->string(120)->notNull()->comment('商品条码'),
                'goods_num'         => $this->integer()->unsigned()->notNull()->comment('退货数量'),
                //  0待处理，1待审核，2待出库（审核通过），3待收款，4 已确认(非现款未结算)，5，已结算，11 ，驳回12，取消
                'pay_price'            => $this->integer(4)->unsigned()->notNull()->defaultValue(0)->comment('状态'),

                'checked_by'        => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('审核人ID'),
                'checked_at'        => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('审核时间'),
                'opinion'           => $this->string(255)->null()->defaultValue('')->comment('审核意见'),

                'shipping_name'     => $this->string(60)->notNull()->defaultValue('')->comment('物流名称'),
                'shipping_no'       => $this->string(40)->notNull()->defaultValue('')->comment('物流单号'),
                'shipping_fee'      => $this->decimal(14, 2)->notNull()->defaultValue(0.00)->comment('实付运费'),
                'shipping_time'     => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('发货时间'),
            ],
            $tableOptions
        );

        $this->createIndex('purchase_back_order_id', $this->tbName, 'purchase_back_order_id');
        $this->createIndex('goods_sn', $this->tbName, 'goods_sn');
        $this->createIndex('checked_by', $this->tbName, 'checked_by');
    }


    public function safeDown()
    {
        $this->dropTable($this->tbName);
    }
}
