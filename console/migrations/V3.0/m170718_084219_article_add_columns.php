<?php

use yii\db\Migration;

/**
 * 文章表添加字段 支持美妆知识库 的文章设置 类型、创建时间、阅读量、关联相册
 * Class m170527_084219_touch_article_add_columns
 */
class m170718_084219_article_add_columns extends Migration
{
    public $tableName = 'o_article';

    public function safeUp()
    {
        $this->addColumn(
            $this->tableName,
            'resource_type',
            " VARCHAR(40) NULL DEFAULT 'article' COMMENT '资源类型' "
        );

        $this->addColumn(
            $this->tableName,
            'click',
            " INT NULL DEFAULT '0' COMMENT '点击量' "
        );

        $this->addColumn(
            $this->tableName,
            'gallery_id',
            " INT NULL DEFAULT '0' COMMENT '关联相册' "
        );

        $this->addColumn(
            $this->tableName,
            'resource_site_id',
            " INT NULL DEFAULT '0' COMMENT '来源站点' "
        );

        $this->addColumn(
            $this->tableName,
            'country',
            " INT NULL DEFAULT '0' COMMENT '区域维度' "
        );

        $this->addColumn(
            $this->tableName,
            'link_cat',
            " INT NULL DEFAULT '0' COMMENT '品类维度' "
        );

        $this->addColumn(
            $this->tableName,
            'scenario',
            " VARCHAR(10) NULL DEFAULT '' COMMENT '应用场景' "
        );

        $this->addColumn(
            $this->tableName,
            'complex_order',
            " INT NULL DEFAULT '0' COMMENT '综合排序值' "
        );

        $this->createIndex('resource_type', $this->tableName, 'resource_type');
        $this->createIndex('click', $this->tableName, 'click');
        $this->createIndex('gallery_id', $this->tableName, 'gallery_id');
        $this->createIndex('resource_site_id', $this->tableName, 'resource_site_id');
        $this->createIndex('country', $this->tableName, 'country');
        $this->createIndex('link_cat', $this->tableName, 'link_cat');
        $this->createIndex('scenario', $this->tableName, 'scenario');
        $this->createIndex('complex_order', $this->tableName, 'complex_order');
    }

    public function safeDown()
    {
        $this->dropIndex('resource_type', $this->tableName);
        $this->dropIndex('gallery_id', $this->tableName);
        $this->dropIndex('click', $this->tableName);
        $this->dropIndex('resource_site_id', $this->tableName);
        $this->dropIndex('country', $this->tableName);
        $this->dropIndex('link_cat', $this->tableName);
        $this->dropIndex('scenario', $this->tableName);
        $this->dropIndex('complex_order', $this->tableName);

        $this->dropColumn($this->tableName, 'resource_type');
        $this->dropColumn($this->tableName, 'click');
        $this->dropColumn($this->tableName, 'gallery_id');
        $this->dropColumn($this->tableName, 'resource_site_id');
        $this->dropColumn($this->tableName, 'country');
        $this->dropColumn($this->tableName, 'link_cat');
        $this->dropColumn($this->tableName, 'scenario');
        $this->dropColumn($this->tableName, 'complex_order');
    }
}
