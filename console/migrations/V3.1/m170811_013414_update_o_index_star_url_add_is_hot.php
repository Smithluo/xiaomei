<?php

use yii\db\Migration;

class m170811_013414_update_o_index_star_url_add_is_hot extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_index_star_url', 'is_hot', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "是否高亮热词"');
    }

    public function safeDown()
    {
        $this->dropColumn('o_index_star_url', 'is_hot');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170811_013414_update_o_index_star_url_add_is_hot cannot be reverted.\n";

        return false;
    }
    */
}
