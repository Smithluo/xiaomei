<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/10 0010
 * Time: 16:08
 */

namespace common\helper;

use common\models\UserRank;

function sortByGoodsNumber($a, $b) {
    if($a->volume_number == $b->volume_number) {
        return 0;
    }
    else {
        return ($a->volume_number < $b->volume_number ? -1 : 1);
    }
}

class PriceHelper
{

    /**
     * 未启用，需要验证是否正确
     * @param $goodsInfo
     * @return string
     */
    public static function getFinalPrice($goodsInfo) {

        $userRank = \Yii::$app->user->identity['user_rank'];
        $memberPriceList = $goodsInfo->memberPrice;

        //当前会员等级的结算价格，为0的话不纳入最终计算
        $memberPrice = 0;
        foreach($memberPriceList as $price) {
            if($price->user_rank == $userRank) {
                $memberPrice = $price->user_price;
            }
        }
        if ($memberPrice == 0) {
            $memberPrice = $goodsInfo->shop_price;
        }

        return NumberHelper::price_format($memberPrice);

//        usort($volumePriceList, 'sortByGoodsNumber');

//        $volumePrice = 0;
//        foreach($volumePriceList as $price) {
//            if($goodsNum > $price->volume_number) {
//                $volumePrice = $price->volume_price;
//            }
//            else {
//                break;
//            }
//        }
//        if($volumePrice == 0) {
//            $volumePrice = $goodsInfo->shop_price;
//        }
//        $final_price = '0'; //商品最终购买价格
//        $volume_price = '0'; //商品优惠价格
//        $promote_price = '0'; //商品促销价格
//        $user_price = '0'; //商品会员价格
//        //取得商品优惠价格列表
//        $price_list = $this->get_volume_price_list($goods_id, '1');
//
//        if (!empty($price_list)) {
//            foreach ($price_list as $value) {
//                if ($goods_num >= $value['number']) {
//                    $volume_price = $value['price'];
//                }
//            }
//        }
//
//        //取得商品促销价格列表
//        /* 取得商品信息 */
//        $sql = "SELECT g.promote_price, g.promote_start_date, g.promote_end_date, " .
//            "IFNULL(mp.user_price, g.shop_price * '" . $_SESSION['discount'] . "') AS shop_price " .
//            " FROM " . $this->pre . "goods AS g " .
//            " LEFT JOIN " . $this->pre . "member_price AS mp " .
//            "ON mp.goods_id = g.goods_id AND mp.user_rank = '" . $_SESSION['user_rank'] . "' " .
//            " WHERE g.goods_id = '" . $goods_id . "'" .
//            " AND g.is_delete = 0";
//        $goods = $this->row($sql);
//
//        /* 计算商品的促销价格 */
//        if ($goods['promote_price'] > 0) {
//            $promote_price = bargain_price($goods['promote_price'], $goods['promote_start_date'], $goods['promote_end_date']);
//        } else {
//            $promote_price = 0;
//        }
//
//        //取得商品会员价格列表
//        $user_price = $goods['shop_price'];
//
//        //比较商品的促销价格，会员价格，优惠价格
//        if (empty($volume_price) && empty($promote_price)) {
//            //如果优惠价格，促销价格都为空则取会员价格
//            $final_price = $user_price;
//        } elseif (!empty($volume_price) && empty($promote_price)) {
//            //如果优惠价格为空时不参加这个比较。
//            $final_price = min($volume_price, $user_price);
//        } elseif (empty($volume_price) && !empty($promote_price)) {
//            //如果促销价格为空时不参加这个比较。
//            $final_price = min($promote_price, $user_price);
//        } elseif (!empty($volume_price) && !empty($promote_price)) {
//            //取促销价格，会员价格，优惠价格最小值
//            $final_price = min($volume_price, $promote_price, $user_price);
//        } else {
//            $final_price = $user_price;
//        }
//
//        //如果需要加入规格价格
//        if ($is_spec_price) {
//            if (!empty($spec)) {
//                $spec_price = $this->spec_price($spec);
//                $final_price += $spec_price;
//            }
//        }
//
//        //返回商品最终购买价格
//        return $final_price;
    }

    /**
     * 获取商品价格段的显示列表, 当前按0-50， 50-100， 100-200， 200-400， 400-800分段
     * @param $min_goods_price
     * @param $max_goods_price
     * @param int $base 最小价格段的最大值，第N个价格段的范围为：$base*(N-1)*(N-1) —— $base*N*N
     */
    public function get_price_range_array($min_goods_price, $max_goods_price, $min_range = 50, $base = 2)
    {
        $min_exponents = NumberHelper::get_power_exponents($base, $min_goods_price / $min_range);
        $max_exponents = NumberHelper::get_power_exponents($base, $max_goods_price / $min_range);

        if ($min_goods_price < $min_range) {
            $result[] = [
                'stprice' => $min_goods_price,
                'edprice' => $min_range,
            ];
        }
        for ($i = $min_exponents; $i <= $max_exponents; $i++) {
            if ($i - $base < 0) {
                continue;
            }

            $result[] = [
                'stprice' => pow($base, $i - $base) * $min_range,
                'edprice' => pow($base, $i - ($base - 1)) * $min_range,
            ];
        }
        $_max_price = pow($base, $max_exponents - ($base - 1)) * $min_range;
        if ($max_goods_price > $_max_price) {
            $result[] = [
                'stprice' => $_max_price,
                'edprice' => $max_goods_price,
            ];
        }

        return $result;
    }
}
