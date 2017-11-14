<?php

namespace common\models;

use common\helper\DateTimeHelper;
use common\helper\GoodsHelper;
use common\helper\OrderGroupHelper;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "o_cart".
 *
 * @property string $rec_id
 * @property string $user_id
 * @property string $session_id
 * @property string $goods_id
 * @property string $goods_sn
 * @property string $product_id
 * @property string $goods_name
 * @property string $market_price
 * @property string $goods_price
 * @property integer $goods_number
 * @property string $goods_attr
 * @property integer $is_real
 * @property string $extension_code
 * @property string $parent_id
 * @property integer $rec_type
 * @property integer $is_gift
 * @property integer $is_shipping
 * @property integer $can_handsel
 * @property string $goods_attr_id
 * @property integer $selected
 */
class Cart extends \yii\db\ActiveRecord
{
    const CART_GENERAL_GOODS            = 0;
    const CART_GROUP_BUY_GOODS          = 1;
    const CART_AUCTION_GOODS            = 2;
    const CART_SNATCH_GOODS             = 3;
    const CART_EXCHANGE_GOODS           = 4;
    const CART_INTEGRAL_EXCHANGE_GOODS  = 5;

    const IS_SELECTED       = 1;
    const IS_NOT_SELECTED   = 0;

    const EXTENSION_CODE_GENERAL   = 'general';
    const EXTENSION_CODE_BATCH     = 'batch';
    const EXTENSION_CODE_GROUP_BUY = 'group_buy';

    public static $flowTypeMap = [
        self::CART_GENERAL_GOODS            => '普通商品',
        self::CART_GROUP_BUY_GOODS          => '团购商品',
//        self::CART_AUCTION_GOODS            => '拍卖商品',
//        self::CART_SNATCH_GOODS             => '夺宝奇兵',
        self::CART_EXCHANGE_GOODS           => '积分商城',  //  EC的积分商城
        self::CART_INTEGRAL_EXCHANGE_GOODS  => '积分兑换',  //  2016-12-5 自定制的积分商城
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'goods_id', 'product_id', 'goods_number', 'is_real', 'parent_id', 'rec_type', 'is_gift', 'is_shipping', 'can_handsel', 'selected'], 'integer'],
//            [['session_id', 'goods_attr'], 'required'],
            [['market_price', 'goods_price'], 'number'],
            [['goods_attr'], 'string'],
            [['session_id'], 'string', 'max' => 32],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 120],
            [['extension_code'], 'string', 'max' => 30],
            [['goods_attr_id'], 'string', 'max' => 255],
            [['goods_attr'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => '购物车记录ID',
            'user_id' => '用户ID',
            'session_id' => 'SessionID',
            'goods_id' => '商品ID',
            'goods_sn' => '商品货号',
            'goods_name' => '商品名',
            'market_price' => '市场价',
            'goods_price' => '购买价',
            'goods_number' => '购买数量',
            'is_real' => '是否真实商品',
            'parent_id' => '配件的主商品ID',
            'rec_type' => '购物车类型',
            'is_gift' => '是否赠品',
            'selected' => '是否选中',

            'can_handsel' => '能否处理',
            'is_shipping' => 'Is Shipping',
            'extension_code' => '扩展码',
            'goods_attr_id' => '商品属性ID',
            'goods_attr' => '商品属性',
            'product_id' => 'Product ID',
        ];
    }

    /**
     * 校验购物车
     * 进入购物车时，检验指定数量的商品是否可购买
     *  不足起售数量的，将购买数量置为起售数量
     *  库存不足的，将选中状态去掉
     * @param int $userId
     * @return array    返回当前不能勾选的商品，前端可设置到session中
     */
    public static function check($userId)
    {
        Yii::warning(' 校验购物车 $userId = '. $userId, __METHOD__);
        $cartGoods = self::find()
            ->joinWith('goods')
//            ->joinWith('moq')   //  不同会员等级对应不同的起售数量 暂时废弃
            ->where(['user_id' => $userId])
            ->all();
        Yii::warning(' 购物车 $cartGoods = '.VarDumper::export($cartGoods), __METHOD__);

        //  修改当前有效的团采活动extension_code 为 group_buy, 过期的团采活动 extension_code 为 general
        $groupBuyMap = GoodsActivity::aliveActivityGoodsActMap(GoodsActivity::ACT_TYPE_GROUP_BUY);
        $groupBuyGoodsIds = array_keys($groupBuyMap);
        Cart::updateAll(['extension_code' => 'group_buy'], ['goods_id' => $groupBuyGoodsIds]);
        Cart::updateAll(['extension_code' => 'general'], ['not in', 'goods_id', $groupBuyGoodsIds]);

        $unSelect = []; //  不可选中的商品
        $unExist = [];  //  不存在的商品
        $underStockGoods = [];
        if (!empty($cartGoods)) {
            foreach ($cartGoods as $item) {

                if (!empty($item->goods)) {
                    //  没上架的商品、已删除的商品、库存低于起订量的商品 不可购买;
                    if (
                        !$item->goods->is_on_sale ||
                        $item->goods->is_delete ||
                        $item->goods['start_num'] > $item->goods['goods_number']
                    ) {
                        if ($item->selected == 1) {
                            $unSelect[] = $item->rec_id;
                        }
                        $underStockGoods[] = $item->goods_id;
                    }
                    //  如果按箱购买的商品当前数量不是按箱的 修正按箱购买的商品 当前正确的购买数量
                    elseif ($item->goods->buy_by_box) {
                        if (!empty($item->goods_number % $item->goods['number_per_box'])) {
                            $reset = round($item->goods_number / $item->goods['number_per_box']) * $item->goods['number_per_box'];
                            //  如果库存充足并且满足最低起售数量
                            if ($reset <= $item->goods['goods_number'] && $reset >= $item->goods['start_num']) {
                                $item->setAttribute('goods_number', $reset);
                                $item->save();
                            } else {
                                //  如果库存不足，则置为不选中
                                if ($item->selected == 1) {
                                    $unSelect[] = $item->rec_id;
                                }
                                $underStockGoods[] = $item->goods_id;
                            }
                        }
                    }
                    //  购物车数量 < 起售数量
                    elseif ($item->goods_number < $item->goods['start_num']) {
                        $item->setAttribute('goods_number', $item->goods['start_num']);
                        $item->save();
                    }
                    //  购物车数量 > 库存数量
                    elseif ($item->goods_number > $item->goods['goods_number']) {
                        if ($item->selected == 1) {
                            $unSelect[] = $item->rec_id;
                        }
                        $underStockGoods[] = $item->goods_id;
                    }
                } else {
                    $unExist[] = $item->rec_id;
                }

            }
        }
        //  修正购物车状态
        Yii::warning(' 不能被选中的商品 $unSelect = '.json_encode($unSelect), __METHOD__);
        if ($unSelect) {
            Cart::updateAll(
                ['selected' => self::IS_NOT_SELECTED],
                [
                    'rec_id' => $unSelect,
                    'user_id' => $userId
                ]
            );
        }
        Yii::warning(' 不存在的商品 $unExist = '.json_encode($unExist), __METHOD__);
        if (!empty($unExist)) {
            Cart::deleteAll(
                [
                    'rec_id' => $unExist,
                    'user_id' => $userId
                ]
            );
        }

        Yii::warning(' 低于库存的商品ID列表 $underStockGoods = '.json_encode($underStockGoods), __METHOD__);

        return $underStockGoods;
    }

    /**
     * 获取购物车中商品对应的商品详情
     * @return \yii\db\ActiveQuery
     */
    public function getGoods() {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取SKU参与的活动ID
     */
    public function getEventToGoods()
    {
        return $this->hasMany(EventToGoods::className(), ['aoods_id' => 'goods_id']);
    }

    /**
     * 获取SKU参与的活动ID
     */
    public function getEvent()
    {
        return $this->hasMany(Event::className(), ['event_id' => 'event_id'])
            ->viaTable(EventToGoods::tableName(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取SKU参与的活动ID
     */
    public function getEventRule()
    {
        return $this->hasMany(Event::className(), ['event_id' => 'event_id'])
            ->viaTable(EventToGoods::tableName(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取SKU参与的当前生效的活动
     */
    public function getEventList()
    {
        $time = DateTimeHelper::getFormatGMTTimesTimestamp();
        return $this->hasMany(Event::className(), ['event_id' => 'goods_id'])
            ->via('eventToGoods')
            ->andOnCondition(['>', Event::tableName().'start_time', $time])
            ->andOnCondition(['<', Event::tableName().'end_time', $time]);
    }


    /**
     * 获取不同会员级别的moq
     * @return \yii\db\ActiveQuery
     */
    public function getMoqs() {
        return $this->hasMany(Moq::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 添加新商品到购物车
     *
     * @param $goods
     * @param $user_id
     * @param $number
     * @return Cart|null
     */
    public static function createFromGoods($goods, $user_id, $number) {
        if (empty($goods) || empty($number) || empty($user_id)) {
            Yii::error('goodsId = '. $goods['goods_id']. ', number = '. $number. ', user_id = '. $user_id, __METHOD__);
            return null;
        }
        //数量不足的
        if ($goods['goods_number'] < $number) {
            $number = $goods['goods_number'];
        }

        if (empty($number)) {
            return null;
        }

        $cart = new Cart();
        $cart->user_id = $user_id;
        $cart->goods_id = $goods['goods_id'];
        $cart->goods_sn = $goods['goods_sn'];
        $cart->goods_name = $goods['goods_name'];
        $cart->market_price = $goods['market_price'];
        $cart->goods_number = $number;
        $cart->is_real = $goods['is_real'];
        $cart->extension_code = $goods['extension_code'];
        $cart->selected = 1;

        return $cart;
    }

    /**
     * 购物车 变更 (商品数量 或 选中态) 后 返回购物车的变更信息
     *  校验被操作商品的 购买数量，不符合条件则修正
     *  价格、满赠、物料信息，重新计算购物车的 满减信息  都在 OrderGroupHelper::cartGoods() 中有处理
     *
     *  修改商品商品、
     */
    public static function updateCart($recId, $goodsNumber, $userId)
    {
        if (!is_int($recId) || !is_int($goodsNumber) || empty($recId)) {
            $result = [
                'code' => 11,
                'message' => '参数错误'
            ];
        }
        else {
            $user = Users::find()
                ->select(['user_rank'])
                ->where(['user_id' => $userId])
                ->one();

            if (empty($user)) {
                $result = [
                    'code' => 12,
                    'message' => '非法访问! 用户不存在'
                ];
            }
            else {
                //  $goodsNumber 传0 表示取消勾选，
                if ($goodsNumber <= 0) {
                    Cart::updateAll(
                        ['selected' => Cart::IS_NOT_SELECTED],
                        [
                            'user_id' => $userId,
                            'rec_id' => $recId,
                        ]
                    );
                }
                //  传值大于0 表示修改购买量或勾选, 需要校验商品的购买数量，不能超过库存，满赠 赠完即止，不过度校验
                else {
                    $cartGoods = self::find()
                        ->joinWith([
                            'goods',
                            'goods.volumePrice',
                        ])->where([
                            'rec_id' => $recId,
                            'user_id' => $userId,
                        ])
                        ->one();
                    if (!empty($cartGoods) && !empty($cartGoods->goods)) {
                        $resetGoodsNum = GoodsHelper::roundBoxNumber(
                            $goodsNumber,
                            $cartGoods->goods->buy_by_box,
                            $cartGoods->goods->number_per_box
                        );

                        $goodsPrice = GoodsHelper::getFinalPrice($cartGoods->goods, $resetGoodsNum, $user->user_rank);

                        Cart::updateAll(
                            [
                                'selected' => Cart::IS_SELECTED,
                                'goods_number' => $resetGoodsNum,
                                'goods_price' => $goodsPrice,
                            ],
                            [
                                'rec_id' => $recId,
                                'user_id' => $userId
                            ]
                        );
                    }
                    else {
                        $result = [
                            'code' => 13,
                            'message' => '非法访问! 你的购物车里没有该商品'
                        ];
                    }
                }

                $data['rec_id'] = $recId;
                $data['goods_price'] = $goodsPrice;
                $data['goods_subtotal'] = '';
                $data['total_desc'] = '';
                //  重新计算购物车的总金额 和 满减活动信息
                $userRankDiscount = Users::getUserRankDiscount($userId);
                $cartGoodsRs = OrderGroupHelper::cartGoods('cart', $userId, $userRankDiscount);

                Yii::warning(' 购物车 商品及活动信息 $cartGoodsRs = '. json_encode($cartGoodsRs), __METHOD__);
                if (!empty($cartGoodsRs)) {
                    $cartGoods = $cartGoodsRs['cartGoods'];
                    $total = $cartGoodsRs['total'];

                    if (!empty($total['bestFullCut'])) {
                        $total['discount'] = $total['bestFullCut']['cut'];
                    }
                    $data['total_amount'] = $total['goods_amount'];
                    $data['total_desc'] = $total['goods_amount'] + $total['shippingFee'] - $total['discount'];
                    $data['goods_number'] = $resetGoodsNum;
                    $data['total_number'] = $total['total_number'];
                    $data['mFullCutMsg'] = $total['bestFullCut']['mFullCutMsg'];
                    $data['pcFullCutMsg'] = $total['bestFullCut']['pcFullCutMsg'];
                    $data['fullCut'] = $total['bestFullCut']['cut'];

                    //  满赠活动信息
                    if (!empty($cartGoods)) {

                        foreach ($cartGoods as $brandOrSupplierId => $cartGroup) {
                            if (!empty($cartGroup) && !empty($cartGroup['goodsList'])) {

                                foreach ($cartGroup['goodsList'] as $goods) {
                                    if ($goods['rec_id'] == $recId) {
                                        //  赠品数组
                                        if (!empty($goods['gift'])) {
                                            foreach ($goods['gift'] as $gift) {
                                                if (!empty($gift['goods_id'])) {}
                                                $data['gift'][] = [
                                                    'gift_goods_id' => $gift['goods_id'],
                                                    'gift_goods_name' => $gift['goods_name'],
                                                    'gift_goods_number' => $gift['goods_number'],
                                                ];
                                            }
                                        }

                                        //  物料数组
                                        if (!empty($goods['wuliaoList'])) {
                                            foreach ($goods['wuliaoList'] as $wuliao) {
                                                if (!empty($wuliao['goods_id'])) {}
                                                $data['wuliao'][] = [
                                                    'gift_goods_id' => $wuliao['goods_id'],
                                                    'gift_goods_name' => $wuliao['goods_name'],
                                                    'gift_goods_number' => $wuliao['goods_number'],
                                                ];
                                            }
                                        }
                                    }

                                }

                            }
                        }

                    }
                }


                $result = [
                    'code' => 0,
                    'data' => $data,
                    'message' => '更新成功'
                ];
            }
        }

        return $result;
    }

    /**
     * 加入商品到购物车
     *
     * 修正低于起订量的商品购买量为起订量
     * 修正按箱购买的商品数量为整箱数
     * 修改按箱购买的商品数量不超过最大整箱数
     * @param $goodsGroup [['goods_id' => $goodsId, 'goods_number' => $goodsNum], [], ...]
     * @param $userId
     * @return array
     */
    public static function addToCart($goodsGroup, $userId)
    {
        $addToCart = [];
        if (!empty($goodsGroup) && is_array($goodsGroup)) {
            foreach ($goodsGroup as $item) {
                if ($item['goods_id'] > 0 && $item['goods_number'] > 0) {
                    $addToCart[$item['goods_id']] = $item['goods_number'];
                }
            }

            if (!empty($addToCart)) {
                $goodsIdList = array_keys($addToCart);
                $goodsList = Goods::find()
                    ->select([
                        'goods_id', 'goods_sn', 'goods_name', 'market_price', 'shop_price', 'discount_disable',
                        'start_num', 'goods_number', 'buy_by_box', 'number_per_box', 'is_real'
                    ])->where([
                        'goods_id' => $goodsIdList,
                        'is_on_sale' => Goods::IS_ON_SALE,
                        'is_delete' => Goods::IS_NOT_DELETE,
                    ])->all();

                if (!empty($goodsList)) {
                    $cartList = Cart::find()
                        ->where(['user_Id' => $userId])
                        ->indexBy('goods_id')
                        ->all();

                    $addRs = [];
                    $msg = '';
                    $userDiscount = Users::getUserRankDiscount($userId);

                    //  如果批量加入购物车的商品中有参与团采的，修正extension_code
                    $groupBuyMap = GoodsActivity::aliveActivityGoodsActMap(GoodsActivity::ACT_TYPE_GROUP_BUY);
                    $groupBuyGoodsIdList = array_keys($groupBuyMap);

                    foreach ($goodsList as $goods) {

                        if ($addToCart[$goods->goods_id] > $goods->goods_number) {
                            $msg .= $goods->goods_name.' 购买数量超出库存 '.$goods->goods_number.';';
                            break;
                        }
                        //  计算按箱购买的最大可购买量
                        if ($goods->buy_by_box && $goods->number_per_box > 0) {
                            $buyNumMax = round($goods->goods_number / $goods->number_per_box) * $goods->number_per_box;
                        } else {
                            $buyNumMax = $goods->goods_number;
                        }

                        //  区分 购物车中是否已有该商品
                        if (isset($cartList[$goods->goods_id])) {
                            $cart = $cartList[$goods->goods_id];
                            $oldGoodsNum = $cart->goods_number;
                                //  校验购买数量
                            $buyNumber = $cart->goods_number + $addToCart[$goods->goods_id];
                            if ($goods->buy_by_box && $goods->number_per_box > 0) {
                                $buyNumber = round($buyNumber / $goods->number_per_box) * $goods->number_per_box;
                            }
                            if ($buyNumber > $buyNumMax) {
                                $buyNumber = $buyNumMax;
                            }

                            $cart->goods_number = $buyNumber;
                            $addRs[] = [
                                'goodsId' => $goods->goods_id,
                                'goodsNum' => $buyNumber - $oldGoodsNum,
                            ];
                        } else {
                            $buyNumber = $addToCart[$goods->goods_id];
                            //  校验起订量量
                            if ($buyNumber < $goods->start_num) {
                                $buyNumber = $goods->start_num;
                            }
                            //  校验 按箱购买的数量
                            if ($goods->buy_by_box && $goods->number_per_box > 0) {
                                $buyNumber = round($buyNumber / $goods->number_per_box) * $goods->number_per_box;
                            }
                            if ($buyNumber > $buyNumMax) {
                                $buyNumber = $buyNumMax;
                            }

                            $cart = self::createFromGoods($goods, $userId, $buyNumber);
                            $addRs[] = [
                                'goodsId' => $goods->goods_id,
                                'goodsNum' => $buyNumber,
                            ];
                        }

                        //  修正商品价格
                        if ($goods->discount_disable) {
                            $goodsPrice = $goods->shop_price;
                        } else {
                            $goodsPrice = $goods->shop_price * $userDiscount;
                        }
                        $cart->goods_price = $goodsPrice;

                        $cart->extension_code = Cart::EXTENSION_CODE_BATCH;    //  批量加入购物车
                        if (in_array($goods->goods_id, $groupBuyGoodsIdList)) {
                            $cart->extension_code = Cart::EXTENSION_CODE_GROUP_BUY;    //  批量加入购物车的团采商品要修正
                        }

                        $cart->save();
                    }

                    $totalNum = self::insertCartNum($userId);

                    $rs = [
                        'code' => 0,
                        'msg' => '成功加入购物车',
                        'data' => [
                            'addRs' => $addRs,
                            'totalNum' => $totalNum,
                        ]
                    ];
                } else {
                    $rs = [
                        'code' => 1,
                        'msg' => '没有在售商品',
                        'data' => []
                    ];
                }
            } else {
                $rs = [
                    'code' => 2,
                    'msg' => '选购商品缺少数量',
                    'data' => []
                ];
            }
        } else {
            $rs = [
                'code' => 3,
                'msg' => '缺少必要参数',
                'data' => []
            ];
        }

        return $rs;
    }

    /**
     * 计算购物车中选中的商品数量
     * @param $userId
     * @return string
     */
    public static function insertCartNum($userId)
    {
        $number = 0;
        if (!empty($userId)) {
            $count = self::find('goods_number')
                ->where([
                    'user_id' => $userId,
                ])->sum('goods_number');
            if (!empty($count)) {
                $number = intval($count);
            }
        }

        return ''.$number;
    }
}
