<?php

use yii\db\Migration;

class m170907_100716_create_table_purchase_supplier_goods_change_log extends Migration
{
    public $tbName = 'o_purchase_supplier_goods_change_log';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB ';

        $this->createTable(
            $this->tbName,
            [
                'id'            => $this->primaryKey()->comment('ID'),
                'supplier_info_id' => $this->integer()->unsigned()->notNull()->comment('供应商ID'),
                'goods_sn'      => $this->string(60)->notNull()->comment('条码'),

                //  裸价、含税价不能同时为空
                'cur_price'     => $this->decimal(14, 2)->null()->comment('当前采购裸价'),
                'cur_tax_price' => $this->decimal(14, 2)->null()->comment('当前采购含税价'),


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
