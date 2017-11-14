<?php

use yii\db\Migration;

/**
 * Class m170907_100400_create_table_purchase_plan
 *
 * 创建采购计划单 表
 * 销售采购计划单 必须关联 order_id
 * 常规采购计划单 默认buyer 为创建人
 */
class m170907_100400_create_table_purchase_plan extends Migration
{
    public $tbName = 'o_purchase_plan';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB ';

        $this->createTable(
            $this->tbName,
            [
                'id'        => $this->primaryKey()->comment('ID'),

                'type'      => $this->integer(1)->notNull()->comment('类型'),  //  0 常规，1 销售
                'order_id'  => $this->integer()->unsigned()->null()->defaultValue(0)->comment('订单ID'),
                //  销售采购计划单转待处理前不能为空，创建时不支持选择，待采购主管分配; 常规采购计划单创建时默认采购员为创建人，支持选择
                'buyer'     => $this->integer()->unsigned()->null()->defaultValue(0)->comment('采购员ID'),

                //  收货人信息
                'province_id' => $this->integer()->unsigned()->notNull()->comment('省份'),
                'city_id' => $this->integer()->unsigned()->notNull()->comment('城市'),
                'district_id' => $this->integer()->unsigned()->null()->comment('县/区'),
                'address' => $this->string(255)->notNull()->comment('详细地址'),

                //  审核之后更新
                'verify_by' => $this->integer()->unsigned()->null()->defaultValue(0)->comment('审核人ID'),
                'verify_at' => $this->dateTime()->null()->comment('审核时间'),
                'opinion'   => $this->string(255)->null()->defaultValue('')->comment('审核意见'),

                //  0:待处理; 1待审核（提交审核）;2：已审核;(通过); 3：已完成（产生采购订单）11:驳回; 12：取消;
                'status'    => $this->integer(3)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
                'created_by' => $this->integer()->unsigned()->notNull()->comment('创建人ID'),
                'created_at' => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('创建时间'),
            ],
            $tableOptions
        );

        $this->createIndex('type', $this->tbName, 'type');
        $this->createIndex('order_id', $this->tbName, 'order_id');
        $this->createIndex('buyer', $this->tbName, 'buyer');
        $this->createIndex('status', $this->tbName, 'status');
    }

    public function safeDown()
    {
        $this->dropTable($this->tbName);
    }
}
