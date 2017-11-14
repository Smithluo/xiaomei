<?php

use yii\db\Migration;

class m170509_062756_create_o_paid_coupon extends Migration
{
    private $tableName = 'o_paid_coupon';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'amount' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('送券需要满足的金额'),
                'event_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('送券的活动'),
                'rule_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('送券的活动规则'),
            ],
            $tableOptions
        );
        $this->createIndex('event_id', $this->tableName, 'event_id');
        $this->createIndex('rule_id', $this->tableName, 'rule_id');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
