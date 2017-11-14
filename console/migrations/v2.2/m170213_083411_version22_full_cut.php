<?php

use yii\db\Migration;
use common\models\OrderInfo;
use common\models\OrderGoods;
use common\models\Users;
use common\helper\PaymentHelpe;

class m170213_083411_version22_full_cut extends Migration
{
    /*public function up()
    {

    }

    public function down()
    {
        echo "m170213_083411_version22_full_cut cannot be reverted.\n";

        return false;
    }*/


    /**
     * 处理历史订单的支付方式 按 新的规则显示 pay_name
     * 不再使用用户等级为0(待审核)状态， 通过is_checked = 0 表示未审核
     * 填充商品实际支付价格，用于均摊优惠金额
     */
    public function safeUp()
    {
        //  处理历史订单的支付方式
        //  支付宝支付
        $updateOrderInfoPayByAlipay = ' UPDATE '.OrderInfo::tableName()." SET pay_name = '支付宝支付' WHERE pay_id = 1 ";
        $this->execute($updateOrderInfoPayByAlipay);

        //  线下支付
        $updateOrderInfoPayByBackend = ' UPDATE '.OrderInfo::tableName()." SET pay_name = '线下支付', pay_id = 5 ".
            " WHERE pay_id = 0 AND pay_name = '' ";
        $this->execute($updateOrderInfoPayByBackend);

        //  积分支付
        $updateOrderInfoPayByBackend = ' UPDATE '.OrderInfo::tableName()." SET  pay_id = 6 WHERE pay_name = '积分支付' ";
        $this->execute($updateOrderInfoPayByBackend);

        //  取消用户等级为0的设置，所有用户最低等级为1
        $updateUserRank = ' UPDATE '.Users::tableName().' SET user_rank = 1 WHERE user_rank = 0 ';
        $this->execute($updateUserRank);

        //  填充商品的实际支付价格
        $updatePayPrice = ' UPDATE '.OrderGoods::tableName().' SET pay_price = goods_price WHERE pay_price = 0 ';
        $this->execute($updatePayPrice);
    }

    public function safeDown()
    {
        echo "m170213_083411_version22_full_cut cannot be reverted.\n";
    }

}
