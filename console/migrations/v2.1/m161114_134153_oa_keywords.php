<?php

use yii\db\Migration;

class m161114_134153_oa_keywords extends Migration
{
    public function safeUp()
    {
        $this->db = Yii::$app->dboa;
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('oa_keywords', [
            'id' => $this->primaryKey(),
            'date' => $this->date()->notNull()->defaultValue('0000-00-00')->comment('日期'),
            'platform' => $this->string(10)->notNull()->comment('平台'),
            'keywords' => $this->string(80)->notNull()->defaultValue(1)->comment('关键词'),
            'count' => $this->integer()->notNull()->defaultValue(1)->comment('搜索次数'),
        ], $tableOptions);
        $this->createIndex('platform', 'oa_keywords', 'platform');
    }

    public function safeDown()
    {
        $this->dropTable('oa_mark');

    }
}
