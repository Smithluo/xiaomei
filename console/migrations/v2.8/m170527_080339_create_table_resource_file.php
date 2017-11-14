<?php

use yii\db\Migration;

/**
 * 资源文件表 给美妆学院 提供视频、下载
 * Class m170527_080339_create_table_resource_file
 */
class m170527_080339_create_table_resource_file extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            'o_resource_file',
            [
                'id' => $this->primaryKey()->comment('ID'),
                'file_name' => $this->string(255)->notNull()->comment('文件名称'),
                'file_path' => $this->string(255)->notNull()->comment('文件路径'),
                'click_times' => $this->integer()->unsigned()->comment('点击次数') //   播放次数/下载量
            ],
            $tableOptions
        );
    }

    public function safeDown()
    {
        $this->dropTable('o_resource_file');
    }

}
