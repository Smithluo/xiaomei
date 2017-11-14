<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_bank_info`.
 */
class m160606_040754_create_o_bank_info extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_bank_info', [
            'id' => $this->primaryKey(),
            'user_name' => $this->string(100)->notNull()->defaultValue('')->comment('开户姓名'),
            'id_card_no' => $this->string(25)->notNull()->defaultValue('')->comment('身份证号'),
            'bank_name' => $this->string(512)->notNull()->defaultValue('')->comment('银行名称'),
            'bank_card_no' => $this->string(512)->notNull()->defaultValue('')->comment('银行卡账号'),
            'bank_address' => $this->string(255)->notNull()->defaultValue('')->comment('银行地址'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_bank_info');
    }
}
