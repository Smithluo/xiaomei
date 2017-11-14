<?php

use yii\db\Migration;

class m170427_022014_create_table_o_activity_manzeng extends Migration
{
    private $tableName = 'o_activity_manzeng';
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('商品'),
                'sort_order' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
                'is_show' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('是否显示'),
                'PRIMARY KEY (goods_id)',
            ],
            $tableOptions
        );
        $this->createIndex('sort_order', $this->tableName, 'sort_order');

        $this->addColumn('o_super_pkg', 'is_show', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT "是否显示"');
        $this->createIndex('is_show', 'o_super_pkg', 'is_show');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
        $this->dropColumn('o_super_pkg', 'is_show');
        return true;
    }

}
