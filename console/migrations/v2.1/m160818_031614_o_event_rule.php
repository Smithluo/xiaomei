<?php

use yii\db\Migration;
//  创建活动策略表
class m160818_031614_o_event_rule extends Migration
{
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_event_rule', [
            'rule_id' => $this->primaryKey()->comment('活动策略ID'),
            //  使用完整表名的 表达式 如 o_order.order_amount > 1000,
            //  提示：先填写文本描述，保存，然后由开发修改成表达式
            'rule_name' => $this->string(80)->notNull()->comment('策略名称'),
            'match_type' => $this->integer()->notNull()->comment('满足类型'),// 'goods_num'、'goods_amount'
            'match_value' => $this->integer()->notNull()->comment('满足数量'),//  件数 或 价值
            'match_effect' => $this->integer()->notNull()->comment('生效范围'),   //'each' | 'all'
            'gift_id' => $this->integer()->notNull()->comment('赠品ID'),
            'gift_num' => $this->integer()->notNull()->comment('赠送数量'),
            //  默认价格待定（商品市场价 OR 注册会员的第一段梯度价格）
            'gift_show_peice' => $this->money(10, 2)->notNull()->comment('赠品单价'),
            //  默认0，直接赠送，不为0 则预留给加价购
            'gift_need_pay' => $this->money(10, 2)->notNull()->defaultValue(0.00)->comment('需要支付'),
            'updated_at' => $this->integer()->notNull()->comment('创建时间')
        ], $tableOptions);

        $this->alterColumn('o_event_rule', 'match_type', " TINYINT NOT NULL DEFAULT 1 COMMENT '满足类型'");
        $this->alterColumn('o_event_rule', 'match_effect', " TINYINT NOT NULL DEFAULT 1 COMMENT '生效范围'");
    }

    public function down()
    {
//        echo "m160818_031614_o_event_rule cannot be reverted.\n";
        $this->dropTable('o_event_rule');
//        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
