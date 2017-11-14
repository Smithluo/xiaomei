<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/8
 * Time: 17:17
 */

namespace common\helper;


class NumberHelper
{
    public static function format_as_money($number)
    {
        return '¥ '.number_format((float)$number, 2, '.', '');
    }

    public static function weightFormat($number) {
        return number_format($number, 3, '.', '');
    }

    public static function price_format($number) {
        return number_format($number, 2, '.', '');
    }

    public static function discount_format($number) {
        return number_format($number, 1, '.', '');
    }

    /**
     * 获取商品价格段的显示列表, 当前按0-50， 50-100， 100-200， 200-400， 400-800分段
     * @param $base_number  底数
     * @param $result   结果值
     * @return float    整数幂指数
     */
    function get_power_exponents($base_number, $result) {
        $i = 0;

        while ($result > pow($base_number, $i)) {
            $i++;
        };

        return $i;
    }
}