<?php

use yii\db\Migration;

class m170801_075329_modify_o_wish_list extends Migration
{
    private $tableName = 'o_wish_list';

    public function safeUp()
    {
        $this->addColumn($this->tableName,'content'," TEXT COMMENT '采购心愿正文' ");
        $this->dropColumn($this->tableName,'cat_name');
        $this->dropColumn($this->tableName,'brand_name');
        $this->dropColumn($this->tableName,'country');
        $this->dropColumn($this->tableName,'goods_name');
        $this->dropColumn($this->tableName,'goods_size');
        $this->dropColumn($this->tableName,'wish_number');
        $this->dropColumn($this->tableName,'consignee');
        $this->dropColumn($this->tableName,'phone');
        $this->dropColumn($this->tableName,'remark');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName,'content');
        $this->addColumn($this->tableName,'cat_name'," VARCHAR(16) NOT NULL DEFAULT '' COMMENT '类别' ");
        $this->addColumn($this->tableName,'brand_name'," VARCHAR(40) NOT NULL DEFAULT '' COMMENT '品牌' ");
        $this->addColumn($this->tableName,'country'," VARCHAR(20) NOT NULL DEFAULT '' COMMENT '国家' ");
        $this->addColumn($this->tableName,'goods_name'," VARCHAR(140) NOT NULL DEFAULT '' COMMENT '商品名称' ");
        $this->addColumn($this->tableName,'goods_size'," VARCHAR(40) NOT NULL DEFAULT '' COMMENT '规格' ");
        $this->addColumn($this->tableName,'wish_number'," INT(11) NOT NULL DEFAULT '' COMMENT '求购数量' ");
        $this->addColumn($this->tableName,'consignee'," VARCHAR(20) NOT NULL DEFAULT '' COMMENT '联系人，默认最近的心愿单联系人或账号中的联系人' ");
        $this->addColumn($this->tableName,'phone'," VARCHAR(20) NOT NULL DEFAULT '' COMMENT '电话号码，默认最近的心愿单联系方式或账号中的联系方式' ");
        $this->addColumn($this->tableName,'remark'," VARCHAR(200) NOT NULL DEFAULT '' COMMENT '备注信息' ");

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170801_075329_modify_o_wish_list cannot be reverted.\n";

        return false;
    }
    */
}
