<?php

use yii\db\Migration;

/**
 * 创建 资源站点 表
 * Class m170527_032914_resource_site
 */
class m170527_032914_create_table_resource_site extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            'o_resource_site',
            [
                'id' => $this->primaryKey()->comment('ID'),
                'site_name' => $this->string(40)->notNull()->comment('站点名称'),
                'site_logo' => $this->string(255)->notNull()->comment('站点logo')
            ],
            $tableOptions
        );

        $this->createIndex('id', 'o_resource_site', 'id');
        $this->createIndex('site_name', 'o_resource_site', 'site_name');
    }

    public function safeDown()
    {
        $this->dropTable('o_resource_site');
    }

}
