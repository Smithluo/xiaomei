<?php

use yii\db\Migration;

class m171027_070142_update_o_brand_add_top_ad_position_pc extends Migration
{
    private $tableName = 'o_brand';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'top_ad_position_id_pc', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "PC端顶部的Banner广告位"');
        $this->createIndex('top_ad_position_id_pc', $this->tableName, 'top_ad_position_id_pc');
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'top_ad_position_id_pc');
        return true;
    }

}
