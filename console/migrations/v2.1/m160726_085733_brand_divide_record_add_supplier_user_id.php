<?php

use yii\db\Migration;

class m160726_085733_brand_divide_record_add_supplier_user_id extends Migration
{
    public function safeUp()
    {
        //  品牌商的分成记录添加supplier_user_id 配合 按品牌商拆单的逻辑
        $this->addColumn('o_brand_divide_record', 'supplier_user_id', "INT NOT NULL DEFAULT '0' COMMENT '品牌商ID' AFTER `brand_id`");
        $this->createIndex('supplier_user_id', 'o_brand_divide_record', 'supplier_user_id');
    }

    public function safeDown()
    {
        echo "m160726_083229_brand_divide_record_add_supplier_user_id cannot be reverted.\n";

        return false;
    }

}
