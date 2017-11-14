<?php

use yii\db\Migration;

class m170717_083630_create_o_analysis_data extends Migration
{
    private $tableName = 'o_analysis_data';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey()->comment('ID'),   //自增id
                'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('用户id'),
                'consignee' => $this->string(60)->notNull()->defaultValue('')->comment('收件人'),
                'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('商品id'),
                'goods_sn' => $this->string(60)->notNull()->defaultValue('')->comment('商品条形码'),
                'goods_name' => $this->string(60)->notNull()->defaultValue('')->comment('商品名称'),
                'goods_number' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('购买数量'),
                'goods_amount'=>$this->decimal(10,2)->notNull()->defaultValue('0.00')->comment('购买金额'),
                'group_id'=>$this->string(22)->notNull()->defaultValue('')->comment('总订单号'),
                'group_status'=>$this->string(10)->notNull()->defaultValue('')->comment('总单状态'),
                'order_amount'=>$this->decimal(10,2)->notNull()->defaultValue('0.00')->comment('总单金额'),
                'cat_id'=>$this->smallInteger(5)->notNull()->defaultValue(0)->comment('品类id'),
                'cat_name'=>$this->string(90)->notNull()->defaultValue('')->comment('品类名称'),
                'brand_id'=>$this->smallInteger(5)->notNull()->unsigned()->defaultValue(0)->comment('品牌id'),
                'brand_name'=>$this->string(60)->notNull()->defaultValue('')->comment('品牌名称'),
                'create_time'=>$this->dateTime()->notNull()->defaultValue('0000-00-00 00:00:00')->comment('下单时间'),
                'pay_time'=>$this->dateTime()->notNull()->defaultValue('0000-00-00 00:00:00')->comment('支付时间'),
                'date'=>$this->date()->notNull()->defaultValue('0000-00-00')->comment('日期')
            ],
            $tableOptions
        );

        $this->createIndex('user_id', $this->tableName, 'user_id');
        $this->createIndex('goods_id',$this->tableName,'goods_id');
        $this->createIndex('cat_id',$this->tableName,'cat_id');
        $this->createIndex('brand_id',$this->tableName,'brand_id');
        $this->createIndex('create_time',$this->tableName,'create_time');
        $this->createIndex('pay_time',$this->tableName,'pay_time');
        $this->createIndex('group_id', $this->tableName, 'group_id');
        $this->createIndex('order_amount',$this->tableName,'order_amount');
        $this->createIndex('date', $this->tableName, 'date');
    }

    public function safeDown()
    {
        echo "m170717_083630_create_o_analysis_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170717_083630_create_o_analysis_data cannot be reverted.\n";

        return false;
    }
    */
}
