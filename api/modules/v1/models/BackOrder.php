<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/11 0011
 * Time: 11:55
 */

namespace api\modules\v1\models;


class BackOrder extends \common\models\BackOrder
{
    public function fields()
    {
        return [
            'backGoods'
        ];
    }

    public function getBackGoods()
    {
        return $this->hasMany(BackGoods::className(), [
            'back_id' => 'back_id',
        ]);
    }
}