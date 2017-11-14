<?php

use yii\db\Migration;

class m170808_021421_update_index_keywords_add_ext extends Migration
{
    private $tableKeywords = 'o_index_keywords';

    public function safeUp()
    {
        $this->addColumn($this->tableKeywords, 'ext', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "扩展场景"');
        $this->createIndex('ext', $this->tableKeywords, 'ext');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableKeywords, 'ext');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170808_021421_update_index_keywords_add_ext cannot be reverted.\n";

        return false;
    }
    */
}
