<?php

use yii\db\Migration;

/**
 * 采购订单
 * Class m170907_100507_create_table_purchase_order
 * 采购订单 对应的物流信息 同步到用户订单的发货信息上去（供应商发货给客户的订单）
 * 采购入库单 对应的入库数量 同步到 采购商品列表记录上的已入库数量
 */
class m170907_100507_create_table_purchase_order extends Migration
{
    public $tbName = 'o_purchase_order';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB ';

        $this->createTable(
            $this->tbName,
            [
                'id'                => $this->primaryKey()->comment('ID'),
                'purchase_plan_id'  => $this->integer()->unsigned()->notNull()->comment('采购计划单ID'),
                'created_by'        => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('创建人ID'),
                'created_at'        => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('创建时间'),

                'checked_by'        => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('审核人ID'),
                'checked_at'        => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('审核时间'),
                'opinion'           => $this->string(255)->null()->defaultValue('')->comment('审核意见'),

                //  下推采购入库之前必填  多个采购订单 可能同一批申请货款
                'purchase_outcome_application_id' => $this->integer()->unsigned()->null()->comment('货款申请单ID'),

                //  退换货订单关联的采购订单,非0 表示是换货订单，关联的是采购订单的ID
                'parent_id'         => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('关联采购单'),

                //  0:待处理;1待审核，2提交审核3:审核通过4已付款，5已发货，6已到货，7已完成，;11驳回 12取消
                'status'            => $this->integer(4)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
                'updated_at'        => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('最后修改时间'),
            ],
            $tableOptions
        );

        $this->createIndex('purchase_plan_id', $this->tbName, 'purchase_plan_id');
        $this->createIndex('created_by', $this->tbName, 'created_by');
        $this->createIndex('checked_by', $this->tbName, 'checked_by');
        $this->createIndex('purchase_payment_request_id', $this->tbName, 'purchase_payment_request_id');
        $this->createIndex('parent_id', $this->tbName, 'parent_id');
        $this->createIndex('status', $this->tbName, 'status');
    }


    public function safeDown()
    {
        $this->dropTable($this->tbName);
    }
}
