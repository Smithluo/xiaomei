<?php

use yii\db\Migration;
use common\models\Shipping;

class m170718_061843_modify_dfgf_name_and_desc extends Migration
{
    public function safeUp()
    {
        Shipping::updateAll(
            [
                'shipping_name' => '全国包邮',
                'shipping_desc' => '全国包邮, 不支持自选物流',
            ],
            [
                'shipping_code' => 'dfgf'
            ]
        );
    }

    public function safeDown()
    {
        Shipping::updateAll(
            [
                'shipping_name' => '全国包邮',
                'shipping_desc' => '全国包邮, 不支持自选物流',
            ],
            [
                'shipping_code' => 'dfgf'
            ]
        );
    }
}
