<?php

use yii\db\Migration;

class m170421_065552_update_o_brand_add_event_id extends Migration
{
    private $tableNameBrand = 'o_brand';
    public function safeUp()
    {
        $this->addColumn($this->tableNameBrand, 'event_id', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "品牌参与的活动"');
        $this->createIndex('event_id', $this->tableNameBrand, 'event_id');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableNameBrand, 'event_id');
        return true;
    }

}
