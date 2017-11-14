<?php

use yii\db\Migration;

class m170907_100659_create_table_purchase_supplier_info extends Migration
{
    public $tbName = 'o_purchase_supplier_info';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM ';

        $this->createTable(
            $this->tbName,
            [
                'id'            => $this->primaryKey()->comment('ID'),

                'company_name'  => $this->string(60)->notNull()->comment('公司名称'),
                'main_cat'      => $this->string(60)->notNull()->comment('主营品类'),
                'contacts'      => $this->string(20)->notNull()->comment('联系人'),
                'tel'           => $this->string(15)->notNull()->comment('联系电话'),
                'contact_info'  => $this->string(255)->null()->comment('联系方式'),

                'level'         => $this->string(10)->null()->comment('星级'),
                'qualification' => $this->text()->null()->comment('资质'),
                'introduction'  => $this->text()->null()->comment('简介'),
                'price_policy'  => $this->string(255)->notNull()->defaultValue('')->comment('价格政策'),  //  按品牌区分
                'shipping_type' => $this->string(255)->notNull()->defaultValue('')->comment('运费政策'),  //  按品牌区分

                //  退货的收货人信息
                'consignee'     => $this->string(20)->notNull()->comment('收件人'),
                'mobile'        => $this->string(15)->notNull()->comment('收件人手机号'),
                'province_id'   => $this->integer()->unsigned()->notNull()->comment('省份'),
                'city_id'       => $this->integer()->unsigned()->notNull()->comment('城市'),
                'district_id'   => $this->integer()->unsigned()->null()->comment('县/区'),
                'address'       => $this->string(255)->notNull()->comment('详细地址'),

                //  结算信息
                'pay_info'      => $this->string(255)->notNull()->comment('收款账号信息'),
                'settlement'    => $this->string(255)->notNull()->comment('结算周期'),
                'balance'       => $this->decimal(14, 2)->notNull()->defaultValue(0.00)->comment('挂账余额'),
                'credit'        => $this->decimal(14, 2)->notNull()->defaultValue(0.00)->comment('账期总额'),

                //  0:待审;1提交审核2:通过;11驳回
                'status'        => $this->integer(4)->unsigned()->notNull()->defaultValue(0)->comment('状态'),
                'updated_at'    => $this->dateTime()->notNull()->defaultValue('2000-00-00 00:00:00')->comment('最后修改时间'),
                'updated_by'    => $this->integer()->unsigned()->notNull()->comment('编辑人ID'),
            ],
            $tableOptions
        );

        $this->createIndex('status', $this->tbName, 'status');
    }


    public function safeDown()
    {
        $this->dropTable($this->tbName);
    }
}
