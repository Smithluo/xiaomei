<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_moq`.
 */
class m160830_073331_create_o_moq extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_moq', [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('商品ID'),
            'moq' => $this->integer(10)->unsigned()->notNull()->defaultValue(10)->comment('商品起订数量'),
            'user_rank' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('用户等级'),
        ], $tableOptions);

        $this->createIndex('goods_id', 'o_moq', ['goods_id', 'user_rank']);
        $this->createIndex('user_rank', 'o_moq', 'user_rank');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_moq');
    }
}
