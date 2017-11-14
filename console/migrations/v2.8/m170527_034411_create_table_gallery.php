<?php

use yii\db\Migration;

/**
 * 美妆知识库 的相册
 * Class m170527_034411_gallery
 */
class m170527_034411_create_table_gallery extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            'o_gallery',
            [
                'gallery_id' => $this->primaryKey()->comment('相册ID'),
                'gallery_name' => $this->string(40)->notNull()->comment('相册名称'),
                'sort_order' => $this->integer(5)->notNull()->defaultValue(30000)->comment('排序值'),
                'is_show' => $this->integer(1)->notNull()->defaultValue(1)->comment('是否显示'),
            ],
            $tableOptions
        );

        $this->createIndex('gallery_id', 'o_gallery', 'gallery_id');
        $this->createIndex('sort_order', 'o_gallery', 'sort_order');
        $this->createIndex('is_show', 'o_gallery', 'is_show');
    }

    public function safeDown()
    {
        $this->dropTable('o_gallery');
    }
}
