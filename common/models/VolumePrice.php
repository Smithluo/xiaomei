<?php

namespace common\models;

use common\helper\NumberHelper;
use Yii;

/**
 * This is the model class for table "o_volume_price".
 *
 * @property integer $price_type
 * @property string $goods_id
 * @property integer $volume_number
 * @property string $volume_price
 */
class VolumePrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_volume_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_type', 'goods_id', 'volume_number'], 'required'],
            [['price_type', 'goods_id', 'volume_number'], 'integer'],
            [['volume_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'price_type' => 'Price Type',
            'goods_id' => 'Goods ID',
            'volume_number' => 'Volume Number',
            'volume_price' => 'Volume Price',
        ];
    }

    /**
     * 按梯度数量从小到大 排序 梯度价格数组
     * @param $volume_price
     * @return array
     */
    public static function sort_volume_price_list($volume_price)
    {
        $result = [];

        if (!empty($volume_price)) {
            foreach ($volume_price as $item) {
                $result[] = [
                    'number' => $item['volume_number'],
                    'price' => $item['volume_price'],
                ];
            }
            //  顺序排列
            usort($result, function($a, $b){
                if ($a['number'] == $b['number']) {
                    return 0;
                } else {
                    //  在第一个参数小于，等于或大于第二个参数时，该比较函数必须相应地返回一个小于，等于或大于 0 的整数。
                    return $a['number'] < $b['number'] ? -1 : 1;
                }
            });

            /*$number_list = array_column($result, 'number');
            array_multisort($number_list, SORT_ASC, SORT_NATURAL, $result);*/
        }

        return $result;
    }

    /**
     * 格式化 梯度价格
     * @param array $volume_price           梯度价格
     * @param float $revised_shop_price     已经乘过折扣的价格
     * @param float $revise_discount        商品对应的折扣 (已考虑是否使用会员等级折扣)
     * @param int $goods_start_num          起售数量
     * @param int $goods_number             购买数量
     * @return array
     */
    public static function volume_price_list_format($volume_price, $revised_shop_price, $revise_discount, $goods_start_num, $goods_number)
    {
        $format_shop_price = NumberHelper::price_format($revised_shop_price);
        Yii::info('$revised_shop_price : '.$revised_shop_price.' format_shop_price '.$format_shop_price.' revise_discount :'.$revise_discount);
        //  如果没有梯度价格
        if (empty($volume_price)) {
            return [
                [
                    'range' => '≥'.(int)$goods_start_num,
                    'range_min' => (int)$goods_start_num,
                    'range_max' => (int)$goods_number,
                    'price' => $revised_shop_price,
                    'format_price' => $format_shop_price,
                ],
            ];
        }

        $volume_price_format = [];
        $end_key = count($volume_price) - 1;
        foreach ($volume_price as $key => $item) {
            $end_number = $item['number'] - 1;
            if ($key == 0 && $item['number'] > 2) {
                $volume_price_format[] = [
                    'range' => (int)$goods_start_num.'-'.(int)$end_number,
                    'range_min' => (int)$goods_start_num,
                    'range_max' => (int)$end_number,
                    'price' => NumberHelper::price_format($revised_shop_price),
                    'format_price' => $format_shop_price,
                ];
            } elseif ($key == 0 && $item['number'] <= 2) {
                $volume_price_format[] = [
                    'range' => '≥'.$goods_start_num,
                    'range_min' => (int)$goods_start_num,
                    'range_max' => (int)$goods_number,
                    'price' => NumberHelper::price_format($revised_shop_price),
                    'format_price' => $format_shop_price,
                ];
            } elseif ($key > 0) {
                $price = $volume_price[$key - 1]['price'] * $revise_discount;
                $volume_price_format[] = [
                    'range' => $volume_price[$key - 1]['number'].'-'.$end_number,
                    'range_min' => (int)$volume_price[$key - 1]['number'],
                    'range_max' => (int)$end_number,
                    'price' => NumberHelper::price_format($price),
                    'format_price' => NumberHelper::price_format($price),
                ];
            }

            if ($key == $end_key) {
                $price = $item['price'] * $revise_discount;
                $volume_price_format[] = [
                    'range' => '≥'.(int)$item['number'],
                    'range_min' => (int)$item['number'],
                    'range_max' => (int)$goods_number,
                    'price' => NumberHelper::price_format($price),
                    'format_price' => NumberHelper::price_format($price),
                ];
            }
        }

        return $volume_price_format;
    }

}
