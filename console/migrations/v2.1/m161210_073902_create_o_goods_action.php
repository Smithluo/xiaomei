<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_goods_action`.
 */
class m161210_073902_create_o_goods_action extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $tableName = 'o_goods_action';
        $this->createTable($tableName, [
            'id' => $this->primaryKey(),
            'user_name' => $this->string(20)->comment('操作者用户名'),
            'goods_id' => $this->smallInteger(5)->comment('商品的ID'),
            'goods_name' => $this->string(120)->comment('商品名称'),
            'shop_price' => $this->money()->comment('修改后的价格'),
            'disable_discount' => $this->boolean()->comment('是否参与会员折扣'),
            'volume_price' => $this->text()->comment('阶梯价'),
            'time' => $this->dateTime()->comment('操作时间'),
        ], $tableOptions);

        $this->createIndex('goods_id', $tableName, 'goods_id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('goods_id', 'o_goods_action');
        $this->dropTable('o_goods_action');
    }
}
