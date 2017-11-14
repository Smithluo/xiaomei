<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_o_brand_user`.
 */
class m160613_084244_create_table_o_brand_admin extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_brand_admin', [
            'id' => $this->primaryKey(),
            'linkman' => $this->string(40)->notNull()->comment('联系人'),
            'mobile' => $this->string(20)->notNull()->comment('联系电话'),
            'back_address' => $this->string()->notNull()->comment('退货地址'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_brand_admin');
    }
}
