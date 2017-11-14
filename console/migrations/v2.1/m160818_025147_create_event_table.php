<?php

use yii\db\Migration;

/**
 * 创建活动表
 */
class m160818_025147_create_event_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_event', [
            'event_id' => $this->primaryKey()->comment('活动ID'),
            'event_name' => $this->string(40)->notNull()->comment('活动名称'),
            'event_desc' => $this->string(255)->comment('规则介绍'),
            'pkg_id' => $this->integer()->notNull()->comment('商品包ID'),   //  活动生效的范围
            'rule_id' => $this->integer()->notNull()->comment('策略ID'), 
            'start_time' => $this->integer()->notNull()->comment('开始时间'),
            'end_time' => $this->integer()->notNull()->comment('结束时间'),
            'updated_at' => $this->integer()->notNull()->comment('创建时间'),
            'updated_by' => $this->integer()->notNull()->comment('创建人ID'),
        ], $tableOptions);

        $this->addColumn('o_event', 'is_active', " TINYINT NOT NULL DEFAULT '0' COMMENT '是否有效'");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('o_event');
    }
}
