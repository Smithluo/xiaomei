<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/10 0010
 * Time: 20:42
 */

namespace api\modules\v1\models;


use common\helper\DateTimeHelper;

class DeliveryOrder extends \common\models\DeliveryOrder
{
    public function fields()
    {
        return [
            'delivery_id' => function ($model) {
                return (int)$model->delivery_id;
            },
            'group_id',
            'invoice_no',
            'add_time' => function($model) {
                return DateTimeHelper::getFormatCNDateTime($model->add_time);
            },
            'deliveryGoods',
        ];
    }
}