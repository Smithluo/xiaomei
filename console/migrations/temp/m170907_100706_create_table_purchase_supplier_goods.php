<?php

use yii\db\Migration;

class m170907_100706_create_table_purchase_supplier_goods extends Migration
{
    public $tbName = 'o_purchase_supplier_goods';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB ';

        $this->createTable(
            $this->tbName,
            [
                'id'            => $this->primaryKey()->comment('ID'),
                'supplier_info_id' => $this->integer()->unsigned()->notNull()->comment('供应商ID'),
                'goods_sn'      => $this->string(60)->notNull()->comment('条码'),
                //  0缺货， 1正常
                'status'        => $this->integer(1)->unsigned()->notNull()->defaultValue(1)->comment('状态'),

                //  含税价、不含税价 至少有一个不能为空,价格可能为0（有赠送的），最低最高采购价 按采购订单中使用的价格比对后回填到这里
                //  计算价格对应的折扣，用o_goods表中的 market_price
                'cur_price'     => $this->decimal(14, 2)->null()->comment('当前采购裸价'),
                'cur_tax_price' => $this->decimal(14, 2)->null()->comment('当前采购含税价'),
                'max_price'     => $this->decimal(14, 2)->notNull()->defaultValue(0.00)->comment('历史最高价'),
                'min_price'     => $this->decimal(14, 2)->notNull()->defaultValue(0.00)->comment('历史最低价'),
                'policy'        => $this->string(255)->null()->defaultValue('')->comment('政策'),

                'created_at'    => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('修改时间'),
                'updated_at'    => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('修改时间'),
                'updated_by'    => $this->integer()->unsigned()->notNull()->comment('编辑人ID'),
            ],
            $tableOptions
        );


        $this->createIndex('supplier_info_id', $this->tbName, 'supplier_info_id');
        $this->createIndex('goods_sn', $this->tbName, 'goods_sn');
        $this->createIndex('updated_by', $this->tbName, 'updated_by');
    }


    public function safeDown()
    {
        $this->dropTable($this->tbName);
    }
}
