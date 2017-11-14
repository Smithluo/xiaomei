<?php

use yii\db\Migration;

/**
 * 文章表添加字段 支持美妆知识库 的文章设置 类型、创建时间、阅读量、关联相册
 * Class m170527_084219_touch_article_add_columns
 */
class m170527_084219_touch_article_add_columns extends Migration
{
    public $tableName = 'o_touch_article';

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

        $this->createIndex('resource_type', $this->tableName, 'resource_type');
        $this->createIndex('click', $this->tableName, 'click');
        $this->createIndex('gallery_id', $this->tableName, 'gallery_id');
        $this->createIndex('resource_site_id', $this->tableName, 'resource_site_id');
    }

    public function safeDown()
    {
        $this->dropIndex('resource_type', $this->tableName);
        $this->dropIndex('gallery_id', $this->tableName);

        $this->dropColumn($this->tableName, 'resource_type');
        $this->dropColumn($this->tableName, 'click');
        $this->dropColumn($this->tableName, 'gallery_id');
        $this->dropColumn($this->tableName, 'resource_site_id');
    }
}
