<?php

use yii\db\Migration;

class m170412_025810_create_goods_index extends Migration
{
    private $tableNameGoodsTag = 'o_goods_tag';
    private $tableNameVolumePrice = 'o_volume_price';
    private $tableNameTags = 'o_tags';
    private $tableNameGoodsAttr = 'o_goods_attr';
    private $tableNameBrand = 'o_brand';

    public function safeUp()
    {
        $this->createIndex('tag_id', $this->tableNameGoodsTag, 'tag_id');
        $this->createIndex('goods_id', $this->tableNameVolumePrice, 'goods_id');
        $this->createIndex('sort', $this->tableNameTags, 'sort');
        $this->createIndex('enabled', $this->tableNameTags, 'enabled');
        $this->alterColumn($this->tableNameGoodsAttr, 'attr_value', 'VARCHAR(64) NOT NULL DEFAULT"" COMMENT "属性值"');
        $this->createIndex('attr_value', $this->tableNameGoodsAttr, 'attr_value');
        $this->createIndex('country', $this->tableNameBrand, 'country');
    }

    public function safeDown()
    {
        $this->dropIndex('tag_id', $this->tableNameGoodsTag);
        $this->dropIndex('goods_id', $this->tableNameVolumePrice);
        $this->dropIndex('sort', $this->tableNameTags);
        $this->dropIndex('enabled', $this->tableNameTags);
        $this->dropIndex('attr_value', $this->tableNameGoodsAttr);
        $this->dropIndex('country', $this->tableNameBrand);
        $this->alterColumn($this->tableNameGoodsAttr, 'attr_value', 'TEXT COMMENT "属性值"');
    }
    
}
