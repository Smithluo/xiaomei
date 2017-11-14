<?php

use yii\db\Migration;

/**
 * 采购入库单
 * Class m170907_100928_create_table_purchase_storage_order
 */
class m170907_100928_create_table_purchase_storage_order extends Migration
{
    public $tbName = 'o_purchase_storage_order';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB ';

        $this->createTable(
            $this->tbName,
            [
                'id'                => $this->primaryKey()->comment('ID'),
                'created_by'        => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('创建人ID'),
                'created_at'        => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('创建时间'),
                //  0:待审;1提交审核2:通过;11驳回 12取消
                'status'            => $this->integer(4)->unsigned()->notNull()->defaultValue(0)->comment('状态'),

                'checked_by'        => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('审核人ID'),
                'checked_at'        => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('审核时间'),
                'opinion'           => $this->string(255)->null()->defaultValue('')->comment('审核意见'),

                'shipping_name'     => $this->string(60)->notNull()->defaultValue('')->comment('物流名称'),
                'shipping_no'       => $this->string(40)->notNull()->defaultValue('')->comment('物流单号'),
                'shipping_fee'      => $this->decimal(14, 2)->notNull()->defaultValue(0.00)->comment('实付运费'),
                'shipping_time'     => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('发货时间'),

                //  采购收货后可下推仓库
                'receive_at'        => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('收货时间'),
                'receive_by'        => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('收货人ID'),
                'storage_at'        => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('仓库入库时间'),
                'storage_by'        => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('入库人ID'),
            ],
            $tableOptions
        );

        $this->createIndex('created_by', $this->tbName, 'created_by');
        $this->createIndex('checked_by', $this->tbName, 'checked_by');
        $this->createIndex('receive_by', $this->tbName, 'receive_by');
        $this->createIndex('storage_by', $this->tbName, 'storage_by');
        $this->createIndex('status', $this->tbName, 'status');
    }


    public function safeDown()
    {
        $this->dropTable($this->tbName);
    }
}
