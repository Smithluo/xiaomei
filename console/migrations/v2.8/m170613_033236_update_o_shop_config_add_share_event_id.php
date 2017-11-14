<?php

use yii\db\Migration;

class m170613_033236_update_o_shop_config_add_share_event_id extends Migration
{
    private $tableName = 'o_shop_config';
    public function up()
    {
        $this->insert($this->tableName, [
            'id'=>'429',
            'parent_id'=>'4',
            'code'=>'share_coupon_event_id',
            'type'=>'text',
            'store_range'=>'',
            'store_dir' =>'',
            'value'=>'0',
            'sort_order'=>'1',
        ]);

        $this->insert($this->tableName, [
            'id'=>'430',
            'parent_id'=>'4',
            'code'=>'share_register_title',
            'type'=>'text',
            'store_range'=>'',
            'store_dir' =>'',
            'value'=>'小美诚品',
            'sort_order'=>'1',
        ]);

        $this->insert($this->tableName, [
            'id'=>'431',
            'parent_id'=>'4',
            'code'=>'share_register_desc',
            'type'=>'text',
            'store_range'=>'',
            'store_dir' =>'',
            'value'=>'邀请注册双方赠券',
            'sort_order'=>'1',
        ]);
    }

    public function down()
    {
        $this->delete($this->tableName, [
            'code' => 'share_coupon_event_id',
        ]);
        $this->delete($this->tableName, [
            'code' => 'share_register_title',
        ]);
        $this->delete($this->tableName, [
            'code' => 'share_register_desc',
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
