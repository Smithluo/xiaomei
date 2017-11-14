<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_tags`.
 */
class m160823_063134_create_o_tags extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_tags', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('类型'),
            'name' => $this->string(6)->notNull()->defaultValue('')->comment('名称'),
            'desc' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'sort' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
            'enabled' => $this->boolean()->notNull()->defaultValue(false)->comment('是否显示'),
            'code' => $this->text()->comment('模版代码'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_tags');
    }
}
