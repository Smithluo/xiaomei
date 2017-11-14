<?php

use yii\db\Migration;

class m161114_133539_oa_mark extends Migration
{
    public function safeUp()
    {
        $this->db = Yii::$app->dboa;
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('oa_mark', [
            'id' => $this->primaryKey(),
            'date' => $this->date()->notNull()->defaultValue('0000-00-00')->comment('日期'),
            'user_id' => $this->integer()->notNull()->comment('用户ID'),
            'login_times' => $this->integer()->notNull()->defaultValue(1)->comment('成功登录次数'),
            'click_times' => $this->integer()->notNull()->defaultValue(1)->comment('浏览页面数量'),
            'order_count' => $this->integer()->notNull()->defaultValue(0)->comment('下单数量'),
            'pay_count' => $this->integer()->notNull()->defaultValue(0)->comment('支付下单数量'),
        ], $tableOptions);
        $this->createIndex('user_id', 'oa_mark', 'user_id');
    }

    public function safeDown()
    {
        $this->dropTable('oa_mark');

    }

}
