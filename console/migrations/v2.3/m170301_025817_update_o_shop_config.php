<?php

use yii\db\Migration;

class m170301_025817_update_o_shop_config extends Migration
{
    private $tableName = 'o_shop_config';

    public function safeup()
    {
        $this->insert($this->tableName,[
            'id'=>'428',
            'parent_id'=>'4',
            'code'=>'servicer_divide_pre',
            'type'=>'text',
            'store_range'=>'',
            'store_dir' =>'',
            'value'=>'0',
            'sort_order'=>'1'
        ]);

    }

    public function down()
    {
        $this->delete($this->tableName, [
            'id' => '428',
        ]);
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
