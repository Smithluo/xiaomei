<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 10:22
 */

namespace brand\models;

use common\models\OrderInfo as BaseOrderInfo;

class CashRecord extends \common\models\CashRecord
{
    /**
     * 关联order表
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(BaseOrderInfo::tableName(), ['order_sn' => 'note']);
    }
}