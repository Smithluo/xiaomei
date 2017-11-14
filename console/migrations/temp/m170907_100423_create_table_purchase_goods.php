<?php

use yii\db\Migration;

/**
 * 采购商品列表
 * 从采购计划单创建 到 采购入库单创建之前 的商品状态流转都在这个表里
 * Class m170907_100423_create_table_purchase_goods
 */
class m170907_100423_create_table_purchase_goods extends Migration
{
    public $tbName = 'o_purchase_goods';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB ';

        $this->createTable(
            $this->tbName,
            [
                'id'                => $this->primaryKey()->comment('ID'),

                'goods_id'          => $this->integer(1)->unsigned()->notNull()->comment('商品ID'),
                'goods_sn'          => $this->string(60)->notNull()->comment('商品条码'),
                'goods_num'         => $this->integer()->notNull()->defaultValue(0)->comment('采购数量'),
                'purchase_price'    => $this->decimal(14, 2)->notNull()->defaultValue(0.00)->comment('采购单价'),
                'normal_num'        => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('实收入库'),
                'abnormal_num'      => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('货损入库'),

                'is_urgent'         => $this->boolean()->notNull()->defaultValue(false)->comment('是否加急'),
                'need_storage'      => $this->boolean()->notNull()->defaultValue(false)->comment('是否入仓'),
                'need_invoice'      => $this->boolean()->notNull()->defaultValue(false)->comment('是否开票'),

                'purchase_plan_id'  => $this->integer()->unsigned()->notNull()->comment('采购计划单ID'),
                //  采购订单ID 创建时可以为空，下推审核时不能为空
                'purchase_order_id' => $this->integer()->unsigned()->null()->defaultValue(0)->comment('采购订单ID'),
                'purchase_storage_id' => $this->integer()->unsigned()->null()->defaultValue(0)->comment('采购入库单ID'),

                //  采购计划已审核、已完成状态则不能修改采购计划ID 采购订单审核通过则不能修改采购单ID
                'status'        => $this->integer(4)->unsigned()->notNull()->defaultValue(0)->comment('状态'),

                'created_at'    => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('创建时间'),
                'updated_at'    => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('最后修改时间'),
            ],
            $tableOptions
        );

        $this->createIndex('goods_id', $this->tbName, 'goods_id');
        $this->createIndex('goods_sn', $this->tbName, 'goods_sn');
        $this->createIndex('purchase_plan_id', $this->tbName, 'purchase_plan_id');
        $this->createIndex('purchase_order_id', $this->tbName, 'purchase_order_id');
        $this->createIndex('purchase_storage_id', $this->tbName, 'purchase_storage_id');
        $this->createIndex('status', $this->tbName, 'status');
    }


    public function safeDown()
    {
        $this->dropTable($this->tbName);
    }
}
