<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/10 0010
 * Time: 10:09
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\Event;
use api\modules\v1\models\Cart;
use api\modules\v1\models\Goods;
use api\modules\v1\models\Users;
use common\helper\ImageHelper;
use common\helper\OrderGroupHelper;
use common\helper\PriceHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use Yii;

/**
 * Class CartController
 *
 * cart/list 用户进入购物车时 修正商品的选中态(不可购买商品不可选中)、按箱购买商品修正数量、参与活动的状态
 * cart/num 修改商品数量要符合库存量和是否按箱购买的规则
 * 所有接口都需要计算已勾选商品参与活动的达成状态
 *
 * @package api\modules\v1\controllers
 */
class CartController extends BaseAuthActiveController
{
    public $modelClass = 'common\models\Cart';

    /**
     * 添加商品到购物车
     * 参数：[{'goodsId': '', 'goodsNumber': ''}]
     * @return array
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionAdd() {

        $userModel = \Yii::$app->user->identity;
        $goodsList = \Yii::$app->request->post('data');

        if(empty($goodsList)) {
            \Yii::error('加入购物车失败', __METHOD__);
            throw new BadRequestHttpException('加入购物车失败', 1);
        }

        foreach ($goodsList as $goods) {

            if (!is_numeric($goods['goodsNumber']) || intval($goods['goodsNumber']) <= 0) {
                \Yii::error('商品数量格式错误，请输入大于0的整数', __METHOD__);
                throw new BadRequestHttpException('商品数量格式错误，请输入大于0的整数', 2);
            } else {

                $addGoodsId = $goods['goodsId'];
                $addGoodsNumber = $goods['goodsNumber'];

                $goodsInfo = Goods::find()
                    ->joinWith('memberPrice')
                    ->joinWith('volumePrice')
                    ->where(['o_goods.goods_id' => $addGoodsId])
                    ->one();

                if(!$goodsInfo) {
                    \Yii::error('商品不存在('. $addGoodsId. ')', __METHOD__);
                    throw new BadRequestHttpException('商品不存在('. $addGoodsId. ')', 3);
                }

                $cartInfo = Cart::findOne(['goods_id' => $addGoodsId, 'user_id' => $userModel->user_id]);
                if ($cartInfo) {
                    //这次加入后预期的商品数量
                    $newCartGoodsNumber = $cartInfo->goods_number + $addGoodsNumber;
                    //超出当前库存了
                    if ($newCartGoodsNumber > $goodsInfo->goods_number) {
                        \Yii::error('商品：'. $goodsInfo->goods_name.' 库存不足', __METHOD__);
                        throw new BadRequestHttpException('商品：'. $goodsInfo->goods_name.' 库存不足', 4);
                    }

                    /* 检查起订数量 */
                    if ($newCartGoodsNumber < $goodsInfo->start_num) {
                        \Yii::error('商品：'. $goodsInfo->goods_name. ' 不满足起订数量', __METHOD__);
                        throw new BadRequestHttpException('商品：'. $goodsInfo->goods_name. ' 不满足起订数量', 5);
                    }
                }
                else {
                    $newCartGoodsNumber = $addGoodsNumber;
                }
                $newCartGoodsNumber = (int)$newCartGoodsNumber;

                /* 是否正在销售 */
                if ($goodsInfo['is_on_sale'] == 0 && !in_array($goodsInfo['goods_id'], [106, 126, 232,233, 139, 386])) {
                    \Yii::error('商品：'. $goodsInfo['goods_name']. ' 已下架', __METHOD__);
                    throw new BadRequestHttpException('商品：'. $goodsInfo['goods_name']. ' 已下架', 6);
                }

                /* 这里有个判断是否允许单独销售的逻辑，暂时不加入接口，后续如果有需求的话可以加在这里 */

                /* 计算商品最终价格 */
                $finalPrice = PriceHelper::getFinalPrice($goodsInfo);

                if(!$cartInfo) {
                    $cartInfo = new Cart();
                    $cartInfo->user_id = $userModel->user_id;
                    $cartInfo->goods_id = $goodsInfo->goods_id;
                    $cartInfo->goods_sn = $goodsInfo->goods_sn;
                    $cartInfo->goods_name = $goodsInfo->goods_name;
                    $cartInfo->goods_price = $finalPrice;
                    $cartInfo->goods_number = $newCartGoodsNumber;
                    $cartInfo->market_price = $goodsInfo->market_price;
                    $cartInfo->is_real = $goodsInfo->is_real;
                    $cartInfo->extension_code = $goodsInfo->extension_code;
                    $cartInfo->is_shipping = $goodsInfo->is_shipping;
                    $cartInfo->rec_type = 0;        //0表示普通商品，1表示团购商品
                }
                $cartInfo->goods_price = $finalPrice;
                $cartInfo->goods_number = $newCartGoodsNumber;

                $cartInfo->selected = 1;

                if ($cartInfo->save()) {
                    $result = [
                        'msg' => '已加入购物车',
                        'goods_price' => $finalPrice,
                        'goods_number' => $newCartGoodsNumber
                    ];
                }
                else {
                    \Yii::error('加入购物车失败，请稍后重试', __METHOD__);
                    throw new ServerErrorHttpException('加入购物车失败，请稍后重试', 7);
                }
                return $result;
            }
        }
    }

    /**
     * 批量加入购物车
     * @return array
     */
    public function actionGroup_add() {
        $data = Yii::$app->request->post('data');
        $goodsList = $data['goods_list'];
        return Cart::addToCart($goodsList, Yii::$app->user->id);
    }

    /**
     * 购物车列表
     *
     * 修正商品的数量 和选中态，返回商品添加 不可购买的flag    考虑吧 OrderController 的 actionCheckout  代码复用
     * 当前只有ios在用，ios升级时要 调用 EventHelper::getValidEventList
     * 小美直发的商品 聚合在一起  goods.supplier_user_id > 0
     *
     * @return array
     */
    public function actionList() {
        $userModel = \Yii::$app->user->identity;

        $cartGoods = Cart::find()->select([
                'o_goods.goods_id',
                'o_goods.goods_name',
                'o_goods.brand_id',
                'o_goods.buy_by_box',
                'o_goods.number_per_box',
                'o_goods.start_num',
                'o_goods.supplier_user_id',
                'o_goods.goods_number max_number',
                'o_cart.rec_id',
                'o_cart.goods_number',
                'o_cart.goods_price',
                'o_cart.selected'
            ])->joinWith([
                'goods' => function($query) {
                    $query->select(['goods_id', 'goods_name', 'goods_thumb', 'brand_id', 'goods_number', 'start_num']);
                },
                'goods.brand' => function($query) {
                    $query->select(['brand_id', 'brand_name']);
                },
                'goods.moqs moqs',
            ])->where(['user_id' => $userModel->user_id])
            ->asArray()
            ->all();

        $brandGoodsInfo = [];

        foreach($cartGoods as &$goods) {

            $gift = Event::getGiftForSingleGoods($goods['goods_id'], $goods['goods_number']);
            if (!empty($gift)) {
                $goods['gift'] = $gift;
            }

            $goods['rec_id'] = intval($goods['rec_id']);
            $goods['goods_id'] = intval($goods['goods_id']);
            $goods['goods_number'] = intval($goods['goods_number']);
            $goods['selected'] = intval($goods['selected']);
            $goods['buy_by_box'] = intval($goods['buy_by_box']);
            $goods['number_per_box'] = intval($goods['number_per_box']);
            $goods['max_number'] = intval($goods['max_number']);
            $goods['goods_thumb'] = ImageHelper::get_image_path($goods['goods']['goods_thumb']);
            $goods['start_num'] = intval($goods['start_num']);

            //如果有等级moq匹配了就按照等级moq
            if (!empty($goods['moqs'])) {
                foreach ($goods['moqs'] as $moq) {
                    if ($moq['user_rank'] == $userModel->user_rank) {
                        $goods['start_num'] = intval($moq['moq']);
                        break;
                    }
                }
            }


            //  按 供应商/品牌 品牌分组
            if ($goods['supplier_user_id'] > 0) {
                $brandOrSupplierId = $goods['supplier_user_id'];
            } else {
                $brandOrSupplierId = $goods['brand_id'];
            }
            $brandGoodsInfo[$brandOrSupplierId]['goods_list'][] = $goods;
            if ($goods['selected']) {
                $brandGoodsInfo[$brandOrSupplierId]['hasValidGoods'] = 1;    //  判定子单是否用于结算
            }

            //  修正支付商品的品牌名称
            if ($brandOrSupplierId == '1257') {
                $brandGoodsInfo[$brandOrSupplierId]['brand_name'] = '小美直发';
                $brandGoodsInfo[$brandOrSupplierId]['brand_id'] = 0;
                $brandGoodsInfo[$brandOrSupplierId]['supplier_user_id'] = 1257;
                if ($goods['selected']) {
                    $total['hasDirectGoods'] = 1;    //  判定是否有 结算的直发商品
                }
            } else {
                $brandGoodsInfo[$brandOrSupplierId]['brand_name'] = $goods['goods']['brand']['brand_name'];
                $brandGoodsInfo[$brandOrSupplierId]['brand_id'] = $brandOrSupplierId;
                $brandGoodsInfo[$brandOrSupplierId]['supplier_user_id'] = 0;
            }
        }

        Yii::info(VarDumper::export($brandGoodsInfo), __METHOD__);

        return [
            'brandGoodsInfo' => $brandGoodsInfo,
        ];
    }

    /**
     * 购物车列表
     *
     * 与common  的 OrderGroupHelper::checkoutCart() 同步
     * 支持 满赠1:n, 支持 物料配比1:n
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionList_v2() {
        $userModel = \Yii::$app->user->identity;

        //  验证用户默认收货地址是否有效
        $validAddress = OrderGroupHelper::checkAddress($userModel->user_id);
        if (!empty($validAddress)) {
            $userRankDiscount = Users::getUserRankDiscount($userModel->user_id);
            $rs = OrderGroupHelper::cartGoods('cart', $userModel->user_id, $userRankDiscount);
            unset($rs['total']['fullCulEventList']);
            unset($rs['total']['couponEventList']);
            unset($rs['total']['fullCulGoodsIdList']);
            unset($rs['total']['couponGoodsIdList']);
            unset($rs['total']['couponEventListFormat']);
            unset($rs['total']['canNotUseCouponList']);
            unset($rs['total']['bestCoupon']);
            unset($rs['total']['canUseCouponList']);

            if (!empty($rs)) {
                return $rs;
            } else {
                throw new ServerErrorHttpException('服务器内部错误，请重试', 2);
            }
        } else {
            throw new BadRequestHttpException('请先完善收货人信息', 1);
        }
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionSelect() {
        $user = \Yii::$app->user->identity;

        $recIds = \Yii::$app->request->post('data');

        if(empty($recIds)) {
            \Yii::error('未选中任何商品', __METHOD__);
            throw new BadRequestHttpException('未选中任何商品', 1);
        }

        Cart::updateAll(['selected' => 1], ['user_id' => $user->user_id, 'rec_id' => $recIds]);

        $carts = Cart::findAll(['user_id' => $user->user_id]);

        $map = [];
        foreach ($carts as $cart) {
            $map[$cart['rec_id']] = $cart['selected'];
        }

        return [
            'selectMap' => $map
        ];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionUnselect() {
        $user = \Yii::$app->user->identity;

        $recIds = \Yii::$app->request->post('data');

        if(empty($recIds)) {
            \Yii::error('未选中任何商品', __METHOD__);
            throw new BadRequestHttpException('未选中任何商品', 1);
        }

        Cart::updateAll(['selected' => 0], ['user_id' => $user->user_id, 'rec_id' => $recIds]);

        $carts = Cart::findAll(['user_id' => $user->user_id]);

        $map = [];
        foreach ($carts as $cart) {
            $map[$cart['rec_id']] = $cart['selected'];
        }

        return [
            'selectMap' => $map
        ];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionDelete() {
        $user = \Yii::$app->user->identity;

        $recIds = \Yii::$app->request->post('data');

        if(empty($recIds)) {
            \Yii::error('请选择商品后再删除', __METHOD__);
            throw new BadRequestHttpException('请选择商品后再删除', 1);
        }

        Cart::deleteAll(['user_id' => $user->user_id, 'rec_id' => $recIds]);

        return [
            'msg' => '删除商品成功'
        ];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionNum() {
        $userModel = \Yii::$app->user->identity;

        $recInfo = \Yii::$app->request->post('data');

        if (empty($recInfo)) {
            \Yii::error('缺少参数');
            throw new BadRequestHttpException('缺少参数', 1);
        }

        $recIds = array_keys($recInfo);

        $carts = Cart::find()->joinWith('goods')->where([
            'user_id' => $userModel->user_id,
        ])->andWhere([
            'rec_id' => $recIds,
        ])->all();

        $result = [];
        $failed = [];
        foreach ($carts as $cart) {
            $newNumber = $recInfo[$cart['rec_id']];

            if (empty($newNumber) || $newNumber < 1) {
                \Yii::error('rec_id = '. $cart['rec_id']. 'new number = '. $newNumber, __METHOD__);
                continue;
            }

            if($recInfo[$cart['rec_id']] > $cart->goods->goods_number) {
                \Yii::error('库存不足 '. $cart->goods->goods_name. ' 库存：'. $cart->goods->goods_number. ', 需求：'. $recInfo[$cart['rec_id']], __METHOD__);
                continue;
            }

            $cart->goods_number = $newNumber;
            if ($cart->save()) {
                $result[$cart['rec_id']] = $newNumber;
            }
            else {
                $failed[$cart['rec_id']] = $newNumber;
            }
        }

        if (!empty($failed)) {
            \Yii::error('保存失败 '. VarDumper::export($failed), __METHOD__);
        }

        return $result;
    }

}