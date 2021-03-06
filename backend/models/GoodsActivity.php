<?php
/**
 * Created by PhpStorm.
 * User: Clark
 * Date: 2016-10-14
 * Time: 10:23
 */

namespace backend\models;

use common\helper\DateTimeHelper;

/**
 * Class GoodsActivity
 * @package backend\models
 * @property string $deposit
 * @property integer $gift_integral
 * @property integer $amount
 * @property string $price
 */
class GoodsActivity extends \common\models\GoodsActivity
{
    public $deposit;
    public $gift_integral;
    public $amount;
    public $price;
    public $price_ladder;
    public $restrict_amount;

    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'deposit' => '保证金',
                'gift_integral' => '赠送积分',
                'amount' => '数量达到(第二梯度)',
                'price' => '享受价格(第二梯度)',
                'price_ladder' => '梯度价格',
                'restrict_amount' => '团购商品总数量(订单商品量达到自动结束，0表示不限制)',

            ]);
    } // TODO: Change the autogenerated stub

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['deposit', 'price'], 'number'],
                [['gift_integral', 'amount'], 'integer'],
                ['price_ladder', 'string'],

                ['deposit', 'default', 'value' => 0],
                ['gift_integral', 'default', 'value' => 0],
                ['restrict_amount', 'default', 'value' => 0],
                [['start_time', 'end_time'], 'string', 'on' => ['insert', 'update']],
                [
                    ['start_time', 'end_time'],
                    'filter',
                    'filter' => function($value) {
                        return DateTimeHelper::getFormatGMTTimesTimestamp(strtotime($value));
                    },
                    'on' => ['insert', 'update']
                ],
            ]
        ); // TODO: Change the autogenerated stub
    }

    public static function formatModel($model)
    {
        $ext_info = unserialize($model->ext_info);
        $model->deposit = $ext_info['deposit'];
        $model->gift_integral = $ext_info['gift_integral'];

        $price_ladder = $ext_info['price_ladder'];
        $model->price_ladder = '';
        foreach ($price_ladder as $item) {
            $model->price_ladder .= ' 达到数量：'.$item['amount'].' 享受价格：'.$item['price'].'</br>';
            $model->amount = $item['amount'];
            $model->price = $item['price'];
        }
        $model->price_ladder = trim($model->price_ladder, '|');

        //  团拼的第二梯度价格
        if (count($price_ladder) < 2) {
            $model->amount = 0;
            $model->price = 0;
        }

        return $model;
    }


}