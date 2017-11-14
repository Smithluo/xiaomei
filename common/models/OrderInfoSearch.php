<?php

namespace common\models;

use backend\models\OrderInfo;
use common\helper\DateTimeHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderInfoSearch represents the model behind the search form about `common\models\OrderInfo`.
 */
class OrderInfoSearch extends OrderInfo
{
    const CS_WAIT_PAY   = 0;
    const CS_WAIT_SHIP  = 1;
    const CS_WAIT_TURN  = 2;
    const CS_COMPLETE   = 3;
    const CS_ALL        = 4;

    public static $cs_status_map = [
        self::CS_WAIT_PAY   => '待付款',
        self::CS_WAIT_SHIP  => '待发货',
        self::CS_WAIT_TURN  => '待退货',
        self::CS_COMPLETE   => '已完成',   //  已确认、已付款、已发货
        self::CS_ALL        => '所有订单',   //  已确认、已付款、已发货
    ];

    public $add_time_start;
    public $add_time_end;
    public $pay_time_start;
    public $pay_time_end;
    public $os_status;   //  订单综合状态 order_cs_status

    public $goods_name;
    public $goods_num;
    public $user_name;

    public $user_province;
    public $user_city;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id', 'order_status', 'shipping_status', 'pay_status', 'country', 'province', 'city', 'district', 'shipping_id', 'pay_id', 'integral', 'from_ad', 'add_time', 'confirm_time', 'pay_time', 'shipping_time', 'recv_time', 'pack_id', 'card_id', 'bonus_id', 'extension_id', 'agency_id', 'is_separate', 'parent_id', 'mobile_pay', 'mobile_order'], 'integer'],
            [['order_sn', 'consignee', 'address', 'zipcode', 'tel', 'mobile', 'email', 'best_time', 'sign_building', 'postscript', 'shipping_name', 'pay_name', 'how_oos', 'how_surplus', 'pack_name', 'card_name', 'card_message', 'inv_payee', 'inv_content', 'referer', 'invoice_no', 'extension_code', 'to_buyer', 'pay_note', 'inv_type', 'brand_id', 'supplier_user_id'], 'safe'],
            [['goods_amount', 'shipping_fee', 'insure_fee', 'pay_fee', 'pack_fee', 'card_fee', 'money_paid', 'surplus', 'integral_money', 'bonus', 'order_amount', 'tax', 'discount'], 'number'],
            [
                [
                    'add_time_start', 'add_time_end', 'pay_time_start', 'pay_time_end',
                    'os_status', 'group_id', 'user_province', 'user_city'
                ],
                'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = OrderInfo::find();

        $query->joinWith(['ordergoods']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'order_id' => SORT_DESC,
                    'pay_time' => SORT_DESC,
                    'goods_name' => [
                        'asc' => ['o_order_goods.goods_name' => SORT_ASC],
                        'desc' => ['o_order_goods.goods_name' => SORT_DESC],
                        'label' => '商品信息'
                    ],
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'order_status' => $this->order_status,
            'shipping_status' => $this->shipping_status,
            'pay_status' => $this->pay_status,
            'country' => $this->country,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'shipping_id' => $this->shipping_id,
            'pay_id' => $this->pay_id,
            'goods_amount' => $this->goods_amount,
            'shipping_fee' => $this->shipping_fee,
            'insure_fee' => $this->insure_fee,
            'pay_fee' => $this->pay_fee,
            'pack_fee' => $this->pack_fee,
            'card_fee' => $this->card_fee,
            'money_paid' => $this->money_paid,
            'surplus' => $this->surplus,
            'integral' => $this->integral,
            'integral_money' => $this->integral_money,
            'bonus' => $this->bonus,
            'order_amount' => $this->order_amount,
            'from_ad' => $this->from_ad,
            'add_time' => $this->add_time,
            'confirm_time' => $this->confirm_time,
//            'pay_time' => $this->pay_time,
            'shipping_time' => $this->shipping_time,
            'recv_time' => $this->recv_time,
            'pack_id' => $this->pack_id,
            'card_id' => $this->card_id,
            'bonus_id' => $this->bonus_id,
            'extension_id' => $this->extension_id,
            'agency_id' => $this->agency_id,
            'tax' => $this->tax,
            'is_separate' => $this->is_separate,
            'parent_id' => $this->parent_id,
            'discount' => $this->discount,
            'mobile_pay' => $this->mobile_pay,
            'mobile_order' => $this->mobile_order,
//            'brand_id' => $this->brand_id
        ]);

        //  支持按购买类型筛选
        if (!empty($this->extension_code)) {
            if ($this->extension_code != OrderInfo::EXTENSION_CODE_GENERAL) {
                $query->andWhere([OrderInfo::tableName().'.extension_code' => $this->extension_code]);
            } elseif ($this->extension_code == OrderInfo::EXTENSION_CODE_GENERAL) {
                $query->andWhere([OrderInfo::tableName().'.extension_code' => ['', OrderInfo::EXTENSION_CODE_GENERAL]]);
            }
        }

        //  筛选当前用户旗下的订单（品牌id对应 或 品牌傻id对应）
        if (isset($params['brand_id']) && $params['brand_id'] && isset($params['supplier_user_id']) && $params['supplier_user_id']) {
            $query->andFilterWhere([
                'or',
                ['brand_id' => $params['brand_id']],
                ['supplier_user_id' => $params['supplier_user_id']]
            ]);
        } elseif (isset($params['supplier_user_id']) && $params['supplier_user_id']) {
            $query->andFilterWhere(['supplier_user_id' => $params['supplier_user_id']]);
        } elseif (isset($params['brand_id']) && $params['brand_id']) {
            $query->andFilterWhere(['brand_id' => $params['brand_id']]);
        }
        if (isset($params['order_status']) && $params['order_status']) {
            $query->andFilterWhere(['order_status' => $params['order_status']]);
        }
        //  筛选订单的综合状态
        if (isset($params['order_cs_status']) && $params['order_cs_status']) {
            $query->andFilterWhere(OrderInfo::$order_cs_status[$params['order_cs_status']]);
        }
        //  筛选订单要显示的状态
        if (isset($params['order_status_to_be_show']) && $params['order_status_to_be_show']) {
            $query->andFilterWhere($params['order_status_to_be_show']);
        }

        //  查询下单时段
        $add_time_start = DateTimeHelper::getFormatGMTTimesTimestamp($this->add_time_start);
        $add_time_end = DateTimeHelper::getFormatGMTTimesTimestamp($this->add_time_end) + 86399;
        $query->andFilterWhere(['between', 'add_time', $add_time_start, $add_time_end]);

        //  查询支付时段
        $pay_time_start = DateTimeHelper::getFormatGMTTimesTimestamp($this->pay_time_start);
        $pay_time_end = DateTimeHelper::getFormatGMTTimesTimestamp($this->pay_time_end) + 86399;
        $query->andFilterWhere(['between', 'pay_time', $pay_time_start, $pay_time_end]);

        $query->andFilterWhere(['like', 'ordergoods.goods_name', $this->goods_name]);

        $query->andFilterWhere(['like', 'order_sn', $this->order_sn])
            ->andFilterWhere(['like', 'consignee', $this->consignee])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'zipcode', $this->zipcode])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'best_time', $this->best_time])
            ->andFilterWhere(['like', 'sign_building', $this->sign_building])
            ->andFilterWhere(['like', 'postscript', $this->postscript])
            ->andFilterWhere(['like', 'shipping_name', $this->shipping_name])
            ->andFilterWhere(['like', 'pay_name', $this->pay_name])
            ->andFilterWhere(['like', 'how_oos', $this->how_oos])
            ->andFilterWhere(['like', 'how_surplus', $this->how_surplus])
            ->andFilterWhere(['like', 'pack_name', $this->pack_name])
            ->andFilterWhere(['like', 'card_name', $this->card_name])
            ->andFilterWhere(['like', 'card_message', $this->card_message])
            ->andFilterWhere(['like', 'inv_payee', $this->inv_payee])
            ->andFilterWhere(['like', 'inv_content', $this->inv_content])
            ->andFilterWhere(['like', 'referer', $this->referer])
            ->andFilterWhere(['like', 'invoice_no', $this->invoice_no])
            ->andFilterWhere(['like', 'to_buyer', $this->to_buyer])
            ->andFilterWhere(['like', 'pay_note', $this->pay_note])
            ->andFilterWhere(['like', 'inv_type', $this->inv_type]);

        return $dataProvider;
    }

    public function searchForExport($params)
    {
        $query = OrderInfo::find();
        $query->joinWith('users users');
        $query->joinWith('provinceRegion provinceRegion');
        $query->joinWith('cityRegion cityRegion');
        $query->joinWith('shipping');
        $query->joinWith([
            'ordergoods ordergoods',
            'ordergoods.goods goods',
        ]);

        $query->with([
            'ordergoods.goods.brand',
            'ordergoods.goods.category',
            'ordergoods.goods.category.parent',
            'ordergoods.goods.goodsAttrRegionWithOutJoin',
            'alipayInfo',
            'wechatPayInfo',
            'yeePayInfo',
            'orderAction',
        ]);

        if (isset($params['page_size']) && $params['page_size'] == 0) {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'order_id' => SORT_DESC,
                    ]
                ],
                'pagination' => [
                    'pagesize' => OrderInfo::find()->count(),
                ],
            ]);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        //  设置 查询的 默认开始时间
        $now = time();
        if (empty($this->add_time_start)) {
            $this->add_time_start = date('Y-m-d', $now - 172800);
        }
        if (empty($this->add_time_end)) {
            $this->add_time_end = date('Y-m-d', $now);
        }

        $userRegionList = Yii::$app->user->identity['userRegion'];
        if (!empty($userRegionList)) {
            $regionIds = [];
            foreach ($userRegionList as $userRegion) {
                $regionIds[] = $userRegion['region_id'];
            }

            $query->andFilterWhere([
                'or',
                [
                    'users.province' => $regionIds
                ],
                [
                    'users.city' => $regionIds
                ],
            ]);
        }


        //  导出订单时 过滤掉未支付和已取消的订单
        /*if ($type == 'export') {
            $query->andFilterWhere(['!=', 'pay_status', 0]);
            $query->andFilterWhere(['!=', 'order_status', OrderInfo::ORDER_STATUS_CANCELED]);
        }*/

        //  支持按购买类型筛选
        if (!empty($this->extension_code)) {
            if ($this->extension_code != OrderInfo::EXTENSION_CODE_GENERAL) {
                $query->andWhere([OrderInfo::tableName().'.extension_code' => $this->extension_code]);
            } elseif ($this->extension_code == OrderInfo::EXTENSION_CODE_GENERAL) {
                $query->andWhere([OrderInfo::tableName().'.extension_code' => ['', OrderInfo::EXTENSION_CODE_GENERAL]]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            OrderInfo::tableName().'.order_id' => $this->order_id,
            OrderInfo::tableName().'.user_id' => $this->user_id,
            'order_status' => $this->order_status,
            'shipping_status' => $this->shipping_status,
            'pay_status' => $this->pay_status,
            'country' => $this->country,
            OrderInfo::tableName().'.province' => $this->province,
            'users.province' => $this->user_province,
            'city' => $this->city,
            'district' => $this->district,
            'shipping_id' => $this->shipping_id,
            'pay_id' => $this->pay_id,
            'goods_amount' => $this->goods_amount,
            'shipping_fee' => $this->shipping_fee,
            'insure_fee' => $this->insure_fee,
            'pay_fee' => $this->pay_fee,
            'pack_fee' => $this->pack_fee,
            'card_fee' => $this->card_fee,
            'money_paid' => $this->money_paid,
            'surplus' => $this->surplus,
            'integral' => $this->integral,
            'integral_money' => $this->integral_money,
            'bonus' => $this->bonus,
            'order_amount' => $this->order_amount,
            'from_ad' => $this->from_ad,
            'confirm_time' => $this->confirm_time,
            'pay_time' => $this->pay_time,
            'shipping_time' => $this->shipping_time,
            'recv_time' => $this->recv_time,
            'pack_id' => $this->pack_id,
            'card_id' => $this->card_id,
            'bonus_id' => $this->bonus_id,
            'extension_id' => $this->extension_id,
            'agency_id' => $this->agency_id,
            'tax' => $this->tax,
            'is_separate' => $this->is_separate,
            'parent_id' => $this->parent_id,
            'discount' => $this->discount,
            'mobile_pay' => $this->mobile_pay,
            'mobile_order' => $this->mobile_order,
            'goods.brand_id' => $this->brand_id,
        ]);


        //  筛选当前用户旗下的品牌
//        if (isset($params['brand_id']) && $params['brand_id']) {
//            if (!is_numeric($params['brand_id'])) {
//                $brand_list = Brand::find('brand_id')
//                    ->where(['like', 'brand_name', $params['brand_id']])
//                    ->asArray()
//                    ->all();
//                if ($brand_list) {
//                    $brand_id = array_column($brand_list, 'brand_id');
//                }
//            } else {
//                $brand_id = $params['brand_id'];
//            }
//
//            if (isset($brand_id) && $brand_id) {
//                $query->andFilterWhere(['brand_id' => $brand_id]);
//            }
//
//        }

        //  筛选订单的综合状态
        /*if ($this->os_status != NULL) {
            $query->andFilterWhere(OrderInfo::$order_cs_status[$this->os_status]);
        }*/

        //  查询下单时段
        $add_time_start = DateTimeHelper::getFormatGMTTimesTimestamp($this->add_time_start);
        $add_time_end = DateTimeHelper::getFormatGMTTimesTimestamp($this->add_time_end) + 86399;
        $query->andFilterWhere(['between', OrderInfo::tableName().'.add_time', $add_time_start, $add_time_end]);

        //  查询支付时段
        if (!empty($this->pay_time_start)) {
            $pay_time_start = DateTimeHelper::getFormatGMTTimesTimestamp($this->pay_time_start);

            if (empty($this->pay_time_end)) {
                $this->pay_time_end = $this->add_time_end;
            }
            $pay_time_end = DateTimeHelper::getFormatGMTTimesTimestamp($this->pay_time_end) + 86399;
            $query->andFilterWhere(['between', OrderInfo::tableName().'.pay_time', $pay_time_start, $pay_time_end]);
        }

        $query->andFilterWhere(['like', 'ordergoods.goods_name', $this->goods_name]);

        $query->andFilterWhere(['like', 'order_sn', $this->order_sn])
            ->andFilterWhere(['like', 'consignee', $this->consignee])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'zipcode', $this->zipcode])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'best_time', $this->best_time])
            ->andFilterWhere(['like', 'sign_building', $this->sign_building])
            ->andFilterWhere(['like', 'postscript', $this->postscript])
            ->andFilterWhere(['like', 'shipping_name', $this->shipping_name])
            ->andFilterWhere(['like', 'pay_name', $this->pay_name])
            ->andFilterWhere(['like', 'how_oos', $this->how_oos])
            ->andFilterWhere(['like', 'how_surplus', $this->how_surplus])
            ->andFilterWhere(['like', 'pack_name', $this->pack_name])
            ->andFilterWhere(['like', 'card_name', $this->card_name])
            ->andFilterWhere(['like', 'card_message', $this->card_message])
            ->andFilterWhere(['like', 'inv_payee', $this->inv_payee])
            ->andFilterWhere(['like', 'inv_content', $this->inv_content])
            ->andFilterWhere(['like', 'referer', $this->referer])
            ->andFilterWhere(['like', 'invoice_no', $this->invoice_no])
            ->andFilterWhere(['like', 'to_buyer', $this->to_buyer])
            ->andFilterWhere(['like', 'pay_note', $this->pay_note])
            ->andFilterWhere(['like', 'inv_type', $this->inv_type])
            ->andFilterWhere(['like', 'group_id', $this->group_id]);

        $query->groupBy(OrderInfo::tableName().'.order_id');

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchForBrand($params)
    {
        $query = OrderInfo::find();
        $query->joinWith('shipping');

        //  当前使用前端分页，获取当前所有符合条件的订单 抛给前端做分页
        $start_time = BrandUser::find()->where(['user_id' => Yii::$app->user->identity->getId()])->one()->reg_time;
        $to_be_show = \brand\models\OrderInfo::getToBeOrders('', $start_time);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $to_be_show['count'],
             ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'order_status' => $this->order_status,
            'shipping_status' => $this->shipping_status,
            'pay_status' => $this->pay_status,
//            'pay_time' => $this->pay_time,
        ]);

        //  筛选当前用户旗下的订单（品牌id对应 或 品牌傻id对应）
        if (isset($params['brand_id']) && $params['brand_id'] && isset($params['supplier_user_id']) && $params['supplier_user_id']) {
            $query->andFilterWhere([
                'or',
                ['brand_id' => $params['brand_id']],
                ['supplier_user_id' => $params['supplier_user_id']]
            ]);
        }
        //  筛选订单要显示的状态
        if (isset($params['order_cs_status']) && $params['order_cs_status']) {
            $query->andFilterWhere($params['order_cs_status']);
        } elseif (isset($params['order_status']) && $params['order_status']) {
            $query->andFilterWhere($params['order_status']);
        }

        //  考虑没有点击查询的条件
        if (isset($params['start_date']) && isset($params['end_date'])) {
            $start_date = strtotime(DateTimeHelper::getGMTDateBegin($params['start_date']));
            if (isset($params['reg_time']) && $start_date < $params['reg_time']) {
                $start_date = $params['reg_time'];
            }
            $end_date = strtotime(DateTimeHelper::getGMTDateEnd($params['end_date']));
            $query->andFilterWhere(['between', 'pay_time', $start_date, $end_date]);
        }

        $query->andFilterWhere(['like', 'order_sn', $this->order_sn])
            ->andFilterWhere(['like', 'consignee', $this->consignee])
            ->andFilterWhere(['like', 'mobile', $this->mobile]);

        return $dataProvider;
    }
}
