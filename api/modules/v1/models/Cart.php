<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27 0027
 * Time: 19:44
 */

namespace api\modules\v1\models;


use common\helper\NumberHelper;

class Cart extends \common\models\Cart
{

    public function fields()
    {

        return [
            'rec_id' => function($model) {
                return (int)$model->rec_id;
            },  //  '购物车记录ID',
            'user_id' => function($model) {
                return (int)$model->user_id;
            },  //  '用户ID',
            'goods_id' => function($model) {
                return (int)$model->goods_id;
            },  //  '商品ID',
            'goods_number' => function($model) {
                return (int)$model->goods_number;
            },  //  '购买数量',
            'is_real' => function($model) {
                return (int)$model->is_real;
            },  //  '是否真实商品',
            'parent_id' => function($model) {
                return (int)$model->parent_id;
            },  //  '配件的主商品ID',
            'rec_type' => function($model) {
                return (int)$model->rec_type;
            },  //  '购物车类型',
            'is_gift' => function($model) {
                return (int)$model->is_gift;
            },  //  '是否赠品',
            'selected' => function($model) {
                return (int)$model->selected;
            },  //  '是否选中',

            'market_price' => function($model) {
                return NumberHelper::price_format($model->market_price);
            },  //  '市场价',
            'goods_price' => function($model) {
                return NumberHelper::price_format($model->goods_price);
            },  //  '购买价',

            'goods_sn' => function($model) {
                return (string)$model->goods_sn;
            },  //  '商品货号',
            'goods_name' => function($model) {
                return (int)$model->goods_name;
            },  //  '商品名',
            'session_id' => function($model) {
                return (int)$model->session_id;
            },  //  'SessionID',

            'goods',
            'moqs',

            /*'can_handsel' => '能否处理',
            'is_shipping' => 'Is Shipping',
            'extension_code' => '扩展码',
            'goods_attr_id' => '商品属性ID',
            'goods_attr' => '商品属性',
            'product_id' => 'Product ID',*/
        ];
    }

//    /**
//     * 检验购物车中的数据，不正确的做修正处理
//     *
//     * 起售数量不足的，将商品数量置为起售数量
//     * 按箱购买的是，把购买数量四舍五入到按箱的数量
//     * 库存不足的，将选中状态去掉
//     */
//    public static function check($userId, $userRank)
//    {
//        $cartList = Cart::find()
//            ->joinWith('goods')
//            ->joinWith('moqs')
//            ->where([
//                'user_id' => $userId,
//            ])->distinct('rec_id')
//            ->all();
//
//        $unSelect = [];         //  需要置为未选中态的商品
//        $canNotBuyGoods = [];   //  购物车中的不可购买商品
//        $resetGoodsNum = [];    //  需要修正购买数量的商品
//        $cartGoods = [];        //  购物车中的所有商品
//        $canBuyGoods = [];      //  购物车中的  可购买商品
//        foreach ($cartList as $item) {
//            $cartGoods[] = $item->goods_id;
//            //  修正起售数量
//            if (!empty($item['moqs'])) {
//                foreach ($item['moqs'] as $moq) {
//                    if ($moq['user_rank'] == $userRank && $moq['moq'] > 0) {
//                        $item['goods']['start_num'] = $moq['moq'];
//                    }
//                }
//            }
//
//            if (!$item['goods']['is_on_sale'] || $item['goods']['is_delete']) {
//                //  没上架的商品和 已删除的商品不可购买
//                $unSelect[] = $item->rec_id;
//                $canNotBuyGoods[] = $item->goods_id;
//            } elseif ($item['goods']['buy_by_box'] && $item->goods_number % $item['goods']['number_per_box']) {
//                //  按箱购买时购买量不是整箱数量  修正按箱购买的商品 为可购买的数量
//
//                //  按箱购买 并且 当前购买数量不是整箱数量 修正数量(价格在算价格前统一重新获取)
//                $maxBoxNum = floor($item['goods']['goods_number'] / $item['goods']['number_per_box']);
//                if ($maxBoxNum) {
//                    $canBuyBox = round($item->goods_number / $item['goods']['number_per_box']);
//                    //  如果四舍五入的装箱数 大于库存，则修正为可购买的数量
//                    if ($canBuyBox > $maxBoxNum) {
//                        $canBuyBox = $maxBoxNum;
//                    }
//                    $reset = $canBuyBox * $item['goods']['number_per_box'];
//
//                    if ($reset > $item['goods']['start_num']) {
//                        $resetGoodsNum[] = [
//                            'rec_id' => $item->rec_id,
//                            'goods_number' => $reset,
//                        ];
//                    } else {
//                        $unSelect[] = $item->rec_id;
//                        $canNotBuyGoods[] = $item->goods_id;
//                    }
//                } else {
//                    $unSelect[] = $item->rec_id;
//                    $canNotBuyGoods[] = $item->goods_id;
//                }
//
//            } elseif ($item['goods']['start_num'] > $item->goods_number) {
//                //  起售数量 > 购物车数量
//                $resetGoodsNum[] = [
//                    'rec_id' => $item->rec_id,
//                    'goods_number' => $item['goods']['start_num'],
//                ];
//            }
//
//            if ($item->goods_number > $item['goods']['goods_number']) {
//                //  购物车数量 > 库存数量
//                if ($item['selected'] == 1) {
//                    $unSelect[] = $item->rec_id;
//                }
//                $canNotBuyGoods[] = $item->goods_id;
//            }
//
//
//            //  批量修改不能购买的商品为未选中状态
//            if ($unSelect) {
//                $unSelectMap = array_unique($unSelect);
//                Cart::updateAll(['selected' => 0], ['rec_id' => $unSelectMap]);
//            }
//            //  修正购物车中数量不对的商品，同时修正价格
//            if ($resetGoodsNum) {
//                foreach ($resetGoodsNum as $item) {
//                    $recModel = Cart::findOne($item['rec_id']);
//                    $recModel->goods_number = $item['goods_number'];
//
//                    $recModel->goods_price = Goods::getGoodsPriceForBuy(
//                        $recModel->goods_id,
//                        $recModel->goods_number,
//                        $userRank
//                    );
//                    $recModel->save();
//                }
//            }
//        }
//        //  格式化 返回数据
//        $canNotBuyGoods = array_unique($canNotBuyGoods);
//        $canBuyGoods = array_diff($cartGoods, $canNotBuyGoods);
//        return [
//            'canNotBuy' => $canNotBuyGoods,
//            'canBuy' => $canBuyGoods
//        ];
//    }

}