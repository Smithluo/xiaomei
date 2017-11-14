<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_servicer_user_info`.
 */
class m160603_023125_create_o_servicer_user_info extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_servicer_user_info', [
            'id' => $this->primaryKey(),
            'servicer_code' => $this->string(32)->notNull()->comment('服务商代码'),
        ], $tableOptions);

        $this->createIndex('servicer_code', 'o_servicer_user_info', ['servicer_code']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_servicer_user_info');
    }
}
