<?php

use yii\db\Migration;

/**
 * 美妆知识库的 相册 图片
 * Class m170527_035156_gallery_img
 */
class m170527_035156_create_table_gallery_img extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            'o_gallery_img',
            [
                'img_id' => $this->primaryKey()->comment('图片ID'),
                'gallery_id' => $this->integer()->notNull()->comment('相册ID'),
                'img_url' => $this->string(255)->notNull()->comment('图片路径'),
                'img_original' => $this->string(255)->notNull()->comment('原图图片路径'),
                'img_desc' => $this->string(255)->null()->comment('图片描述'),
                'sort_order' => $this->integer()->notNull()->defaultValue(30000)->comment('排序值'),
            ],
            $tableOptions
        );

        $this->createIndex('img_id', 'o_gallery_img', 'img_id');
        $this->createIndex('gallery_id', 'o_gallery_img', 'gallery_id');
        $this->createIndex('sort_order', 'o_gallery_img', 'sort_order');

        $this->alterColumn(
            'o_gallery_img',
            'sort_order',
            " SMALLINT UNSIGNED NOT NULL DEFAULT '30000' COMMENT '排序值' "
        );
    }

    public function safeDown()
    {
        $this->dropTable('o_gallery_img');
    }
}
