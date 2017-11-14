<?php

use yii\db\Migration;

class m170801_111313_modify_o_feedback extends Migration
{
    private $tableName = 'o_feedback';

    public function safeUp()
    {
        $this->addColumn($this->tableName,'user_phone'," VARCHAR(20) NOT NULL DEFAULT '' COMMENT '联系号码' ");
        $this->alterColumn($this->tableName,'msg_type',
            " TINYINT(1) NOT NULL DEFAULT '0' COMMENT '反馈类型 0为默认（微信端提交）1意见建议 2BUG提交 3播放速度 4其他问题'");
		$this->alterColumn($this->tableName,'message_img',
            " TEXT COMMENT ''");
    }

    public function safeDown()
    {
		$this->alterColumn($this->tableName,'message_img',
            " VARCHAR(255) NOT NULL DEFAULT '0' COMMENT ''");
		$this->alterColumn($this->tableName,'msg_type',
            " TINYINT(1) NOT NULL DEFAULT '0' COMMENT ''");
        $this->dropColumn($this->tableName,'user_phone');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170801_111313_modify_o_feeedback cannot be reverted.\n";

        return false;
    }
    */
}
