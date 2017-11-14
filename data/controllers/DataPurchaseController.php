<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3 0003
 * Time: 18:03
 */

namespace data\controllers;

use common\helper\CacheHelper;
use common\models\Brand;
use common\models\Category;
use common\models\Goods;
use common\models\OrderGroup;
use common\models\Users;
use data\models\DataAnalysis;
use Yii;
use yii\helpers\ArrayHelper;

class DataPurchaseController extends DataBaseAuthController
{
    public $enableCsrfValidation = false;

    /**
     *下载订单流水号
     */
    public function actionDownloadOrderList()
    {
        $order = $this->getOrderListData();
        \moonland\phpexcel\Excel::export([
            'format' => 'Excel5',
            'fileName' => '订单流水列表',   //
            'models' => $order,
            'columns' => [
                'group_id',
                'consignee',
                'group_status',
                'order_amount',
                'create_time',
                'pay_time'
            ],
            'headers' => [
                'group_id' => '总单号',
                'consignee' => '收货人',
                'group_status' => '支付状态',
                'order_amount' => '总货款',
                'create_time' => '下单时间',
                'pay_time' => '支付时间'
            ],
        ]);
    }

    /**
     * 订单流水信息
     */
    public function actionGetOrderList()
    {
        if (Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        $order = $this->getOrderListData();
        //分页
        if (empty($order)) {
            return $this->throwError(['code' => -10, ]);
        }
        if (isset($order['code'])) {
            return $order;
        }
        $this->getParamPagination();

        $data    = $this->getDataPagination($order);
        $pageSum = $this->pageSum;
        return $this->throwError(['code' => 0, 'pageSum' => $pageSum, 'data' => $data]);
    }

    /**
     * 获取列表信息
     * @return array
     */
    private function getOrderListData()
    {
        //获取时间 商品等条件信息
        $param = $this->getParamData();
        if (isset($param['code'])) {
            return $param;
        }
        $key       = 'purchaseOrderList user:' . $this->user_id . ' time:' . $this->start_time . 'to' . $this->end_time
            . ' where' . serialize($this->andWhere);
        $redis     = Yii::$app->redis;
        $redisData = $redis->get($key);
        if ($redisData) {
            $order = unserialize($redisData);
        } else {
            //获取订单信息 -总订单号-收货人-支付状态-总货款-下单时间-支付时间
            $order = DataAnalysis::find()->where([
                'user_id' => $this->user_id
            ])->andWhere([
                'between', 'date', $this->start_time, $this->r_end_time
            ]);
            if ($this->andWhere != -1) {
                $order->andWhere($this->andWhere);
            }
            $order = $order->asArray()->all();
            if (!$order) {
                //TODO 无结果抛出异常
                return $this->throwError(['code' => -10]);
            }
            //去重操作 -只保留总订单信息
            $newOrder = array();
            foreach ($order as $orderStr) {
                if (!isset($newOrder[$orderStr['group_id']])) {
                    $newOrder[$orderStr['group_id']] = array(
                        'group_id' => (string)$orderStr['group_id'],
                        'consignee' => (string)$orderStr['consignee'],
                        'group_status' => (string)$orderStr['group_status'],
                        'order_amount' => (string)number_format($orderStr['order_amount'], 2),
                        'create_time' => (string)$orderStr['create_time'],
                        'pay_time' => (string)$orderStr['pay_time']
                    );
                }
            }
            $order = array_values($newOrder);
            //排序-分页 TODO 后续优化 先获取排序条件再带上进到数据库查询中- 分页也是如此
            $this->getParamSort();
            //抽取数组
            $orderAmount = array();
            $createTime  = array();
            $payTime     = array();
            foreach ($order as $orderStr) {
                $orderAmount[] = $orderStr['order_amount'];
                $createTime[]  = $orderStr['create_time'];
                $payTime[]     = $orderStr['pay_time'];
            }
            $sortMap   = array(
                'order_amount' => $orderAmount,
                'create_time' => $createTime,
                'pay_time' => $payTime
            );
            $sortByMap = array(
                'desc' => SORT_DESC,
                'asc' => SORT_ASC
            );
            if (!isset($sortMap[$this->sort])) {
                //TODO 抛出异常
                return $this->throwError(['code' => 2, 'param' => 'sort']);
            }

            array_multisort($sortMap[$this->sort], $sortByMap[$this->sortBy], $order);
            $redis->set($key, serialize($order));
        }
        //        var_dump($order);
        return $order;
    }

    /**
     * 下载列表Excel文件
     */
    public function actionDownloadPurchaseList()
    {

        $order = $this->getPurchaseListData();
        \moonland\phpexcel\Excel::export([
            'format' => 'Excel5',
            'fileName' => '采购汇总列表',   //
            'models' => $order,
            'columns' => [
                'goods_name',
                'goods_sn',
                'brand_name',
                'cat_name',
                'goods_amount',
                'goods_number',
                'order_number'
            ],
            'headers' => [
                'goods_name' => '商品名称',
                'goods_sn' => '条形码',
                'brand_name' => '品牌',
                'cat_name' => '品类',
                'goods_amount' => '采购金额',
                'goods_number' => '采购数量',
                'order_number' => '下单次数'
            ],
        ]);
        //TODO 这里没办法执行到
        return $this->throwError(['code' => 0]);
    }

    /**
     * 用户采购汇总列表获取
     */
    public function actionGetPurchaseList()
    {
        if (Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        $purchase = $this->getPurchaseListData();
        if (isset($purchase['code'])) {
            return $purchase;
        }
        //分页
        $this->getParamPagination();
        $data    = $this->getDataPagination($purchase);
        $pageSum = $this->pageSum;
        return $this->throwError(['code' => 0, 'pageSum' => $pageSum, 'data' => $data]);
    }

    private function getPurchaseListData()
    {
        //获取商品名称、条码、品牌、品类、采购金额、采购数量
        $param = $this->getParamData();
        if (isset($param['code'])) {
            return $param;
        }
        /**
         * 增加redis优化 将所有列表结果保存到redis内存中
         */
        $key       = 'purchaseList user:' . $this->user_id . ' time:' . $this->start_time . 'to' . $this->end_time
            . ' where:' . serialize($this->andWhere);
        $redis     = Yii::$app->redis;
        $redisData = $redis->get($key);
        if ($redisData) {
            $purchase = unserialize($redisData);
        } else {
            //查询结果
            $purchase = DataAnalysis::find()->where([
                'user_id' => $this->user_id
            ])->andWhere([
                'between', 'date', $this->start_time, $this->r_end_time
            ]);
            if ($this->andWhere != -1) {
                $purchase->andWhere($this->andWhere);
            }
            $purchase->asArray();
            $purchase = $purchase->all();
            if (!$purchase) {
                //TODO 结果为空 抛出无结果异常
                return $this->throwError(['code' => -10]);
            }
            //去重累加 --筛选字段
            $newPurchase = array();
            foreach ($purchase as $purchaseStr) {
                if (isset($newPurchase[$purchaseStr['goods_id']])) {
                    //累加采购金额 和 采购数量
                    $newPurchase[$purchaseStr['goods_id']]['goods_amount'] += $purchaseStr['goods_amount'];
                    $newPurchase[$purchaseStr['goods_id']]['goods_amount'] = (string)$newPurchase[$purchaseStr['goods_id']]['goods_amount'];
                    $newPurchase[$purchaseStr['goods_id']]['goods_number'] += $purchaseStr['goods_number'];
                    $newPurchase[$purchaseStr['goods_id']]['goods_number'] = (string)$newPurchase[$purchaseStr['goods_id']]['goods_number'];
                    if (!in_array($purchaseStr['group_id'], $newPurchase[$purchaseStr['goods_id']]['group_id'])) {
                        $newPurchase[$purchaseStr['goods_id']]['group_id'][] = $purchaseStr['group_id'];
                        $newPurchase[$purchaseStr['goods_id']]['order_number'] += 1;   //累加采购订单数
                        $newPurchase[$purchaseStr['goods_id']]['order_number'] = (string)$newPurchase[$purchaseStr['goods_id']]['order_number'];
                    }
                } else {
                    $newPurchase[$purchaseStr['goods_id']] = array(
                        'goods_id' => $purchaseStr['goods_id'], //商品id
                        'goods_name' => (string)$purchaseStr['goods_name'], //商品名称
                        'goods_sn' => (string)$purchaseStr['goods_sn'], //条形码
                        'brand_id' => $purchaseStr['brand_id'], //品牌id
                        'brand_name' => (string)$purchaseStr['brand_name'], //品牌名称
                        'cat_id' => $purchaseStr['cat_id'], //品类id
                        'cat_name' => (string)$purchaseStr['cat_name'], //品类名称
                        'goods_amount' => (string)$purchaseStr['goods_amount'], //采购金额
                        'goods_number' => (string)$purchaseStr['goods_number'], //采购数量
                        'group_id' => array($purchaseStr['group_id']),  //总单id-用以记录订单数
                        'order_number' => 1    //订单数 判断总订单一样时则不累加订单次数
                    );
                }
            }
            //去键值
            $purchase = array_values($newPurchase);

            reset($purchase);
            //排序 TODO 后续优化 先获取排序条件再带上进到数据库查询中- 分页也是如此
            $this->getParamSort();
            //抽取排序列
            $goodsAmount = array();
            $goodsNumber = array();
            $orderNumber = array();
            foreach ($purchase as $purchaseStr) {
                $goodsAmount[] = $purchaseStr['goods_amount'];
                $goodsNumber[] = $purchaseStr['goods_number'];
                $orderNumber[] = $purchaseStr['order_number'];
            }
            //排序映射关系
            $sortMap    = array(
                'goods_amount' => $goodsAmount,
                'goods_number' => $goodsNumber,
                'order_number' => $orderNumber
            );
            $sortByMap  = array(
                'asc' => SORT_ASC,
                'desc' => SORT_DESC
            );
            $this->sort = 'goods_amount';
            if (!isset($sortMap[$this->sort])) {
                return $this->throwError(['code' => 2, 'param' => 'sort']);
            }
            array_multisort($sortMap[$this->sort], $sortByMap[$this->sortBy], $purchase);
            //并且unset掉group_id元素
            foreach ($purchase as &$newPurchaseStr) {
                $newPurchaseStr['goods_amount'] = (string)number_format($newPurchaseStr['goods_amount'], 2);
                unset($newPurchaseStr['group_id']);
                unset($newPurchaseStr['goods_id']);
                unset($newPurchaseStr['brand_id']);
                unset($newPurchaseStr['cat_id']);
            }
            $redis->set($key, serialize($purchase));
        }
        return $purchase;
    }

    public $pageSum;

    /**
     * 进行分页
     */
    private function getDataPagination($array)
    {
        $this->pageSum = ceil(count($array) / $this->pageSize);
        if ($this->pageSum < $this->page) {
            return $this->throwError(['code' => 2, 'msg' => '页码不正确', 'param' => 'page']);
        }
        $data = array_slice($array, $this->offset, $this->pageSize);
        return $data;
    }

    public $pageSize;
    public $page;
    public $offset; //分页的第一条数据下标

    /**
     * 分页信息
     */
    private function getParamPagination()
    {
        $this->pageSize = 10;
        if (!isset($this->param['page'])) {
            return $this->throwError(['code' => 1, 'param' => 'page']);
        }
        $this->page = $this->param['page'];
        if ($this->page < 1) {
            return $this->throwError(['code' => 2, 'param' => 'page']);
        }
        $this->offset = ($this->page - 1) * $this->pageSize;
    }

    public $sort;
    public $sortBy;

    /**
     * 获取排序信息参数
     */
    private function getParamSort()
    {
        if (!isset($this->param['sort'])) {
            return $this->throwError(['code' => 1, 'param' => 'sort']);
        }
        $this->sort   = $this->param['sort'];
        $this->sortBy = isset($this->param['sort_by']) ? $this->param['sort_by'] : 'desc';
    }

    public function actionGetUserInfo()
    {
        $phone = Yii::$app->request->post('phone');
        $this->getParamData();
        if (empty($phone)) {
            return $this->throwError(['code' => 1, 'param' => 'phone']);
        }
        $user  = Users::find()->alias('u')->select([
            'u.user_id', 'u.nickname', 'u.user_name', 'p.region_name as province', 'c.region_name as city',
            'u.mobile_phone', 'u.user_rank', 'u.user_type', 'u.company_name', 'u.reg_time', 'u.last_login',
        ])->where([
            'mobile_phone' => $phone
        ])->andWhere([
            'not',
            [
                'mobile_phone' => '',
            ],
        ])->joinWith([
            'provinceRegion p',
            'cityRegion c',
        ])->asArray()->one();
        if (!$user) {
            return $this->throwError(['code' => -10]);
        }
        $order = OrderGroup::find()->select([
            'pay_time'
        ])->orderBy([
            'pay_time' => SORT_DESC
        ])->where([
            'user_id' => $user['user_id']
        ])->asArray()->limit(1)->one();
        $data  = array(
            'user_id' => $user['user_id'],
            'nickname' => isset($user['nickname']) ? $user['nickname'] : '--',
            'user_name' => isset($user['user_name']) ? $user['user_name'] : '--',
            'province' => isset($user['province']) ? $user['province'] : '--',
            'city' => isset($user['city']) ? $user['city'] : '--',
            'mobile_phone' => isset($user['mobile_phone']) ? $user['mobile_phone'] : '--',
            'user_rank' => Users::$user_rank_map[$user['user_rank']],
            'user_type' => Users::$user_type_map[$user['user_type']],
            'company_name' => isset($user['company_name']) ? $user['company_name'] : '--',
            'reg_time' => date('Y-m-d', $user['reg_time']),
            'last_login' => date('Y-m-d', $user['last_login']),
            'last_pay_time' => date('Y-m-d', $order['pay_time'])
        );
        return $this->throwError(['code' => 0, 'data' => $data]);
    }

    public function actionGetPurchaseData()
    {
        //载入接收参数
        $param = $this->getParamData();
        if (isset($param['code'])) {
            return $param;
        }
        // 指定周期
        if (!isset($this->param['cycle'])) {
            return $this->throwError(['code' => 1, 'param' => 'cycle']);
        }
        $cycle = $this->param['cycle'];
        $cycleMap = array(
            'day' => '1 day',
            'week' => '1 week',
            'month' => '1 month',
            'quarter' => '3 month',
            'year' => '1 years'
        );
        if (!isset($cycleMap[$cycle])) {
            //TODO 抛出异常
            return $this->throwError(['code' => 2, 'param' => 'cycle']);
        }
        $cycle = $cycleMap[$cycle];

        /**判断是否有缓存 --将开始时间、结束时间、用户id、andWhere、周期作为redis的key
         *然后去查看在redis内存中是否有该key，若有直接拉取内存中的值 否则调用下面的查询并且写入到redis中
         */
        $key       = 'purchaseData user:' . $this->user_id . ' time:' . $this->start_time . 'to' . $this->end_time
            . ' where:' . serialize($this->andWhere) . ' cycle:' . $cycle;
        $redis     = Yii::$app->redis;
        $redisData = $redis->get($key);
        if ($redisData) {
            //取到了redis的值，反序列化一下
            $data = unserialize($redisData);
        } else {
            //redis 取不到数据，需要重新计算并且写到内存去
            //遍历时间
            $cycleDate = array();
            //周期时间的下一节点
            $nextDate = date('Y-m-d', strtotime($this->start_time . '+' . $cycle));
            if ($nextDate > date('Y-m-d', strtotime($this->end_time))) {
                //TODO 这里是筛选周期无数据
                return $this->throwError(['code' => -10, 'msg' => '所选周期无数据']);
            }
            $data = array();
            for ($start = strtotime($this->start_time); $start < strtotime($this->end_time); $start += 60 * 60 * 24)  //按天遍历
            {
                //取时间日期
                $day = date('Y-m-d', $start);
                //周期时间
                if ($day !== $nextDate) {
                    $cycleDate[] = $day;
                    if (strtotime($day . '+ 1day') == strtotime($nextDate)) {
                        //按周期取数据
                        $cycleData          = $this->getUserData($cycleDate[0], end($cycleDate));
                        $data['date'][]     = $day;
                        $data['amount'][]   = $cycleData['amount'];
                        $data['number'][]   = $cycleData['number'];
                        $data['orderNum'][] = $cycleData['orderNum'];
                        $data['brandNum'][] = $cycleData['brandNum'];
                        $data['skuNum'][]   = $cycleData['skuNum'];
                        $cycleDate = array();
                        $nextDate  = date('Y-m-d', strtotime($nextDate . '+' . $cycle));
                    }
                }
            }

            if (count($cycleDate) != 0) {
                //最后执行一次剩下的 作为单独的一个周期
                $cycleData          = $this->getUserData($cycleDate[0], end($cycleDate));
                $data['date'][]     = end($cycleDate);
                $data['amount'][]   = $cycleData['amount'];
                $data['number'][]   = $cycleData['number'];
                $data['orderNum'][] = $cycleData['orderNum'];
                $data['brandNum'][] = $cycleData['brandNum'];
                $data['skuNum'][]   = $cycleData['skuNum'];
            }

            //取采购占比数据 -计算排名、占比
            $purchaseProportion      = $this->getPurchaseProportion($this->start_time, $this->r_end_time);
            $totalData               = $this->getUserData($this->start_time, $this->r_end_time);
            $rankNum                 = array_search($totalData['amount'], $purchaseProportion['goodsAmount']) + 1;
            $userNum                 = count($purchaseProportion['goodsAmount']);
            if ($totalData['amount'] == 0) {
                $proportion = '--';
            } else {
                $proportion = round(($totalData['amount'] / array_sum($purchaseProportion['goodsAmount'])) * 100, 3) . '%';
            }
            if ($userNum == 0 ) {
                $rank =  '--';
            }else {
                $rank = $rankNum . '/' . $userNum;
            }
            $totalData['rank']       = (string)$rank;
            $totalData['proportion'] = (string)$proportion;

            $totalData['amount']     = (string)number_format($totalData['amount'], 2);
            //TODO 这里先注释掉，后续需要进行大类跟大类排名
            if (isset($this->cat_id)) {
                $totalData['rank'] = '--';
            }
            $data = array(
                'totalData' => $totalData,
                'detailData' => $data
            );
            //插入到redis内存中
            $redis->set($key, serialize($data));
        }
        return $this->throwError([
            'code' => 0,
            'msg' => '获取成功',
            'data' => $data
        ]);
    }

    /**
     * 抛出错误
     */
    private function throwError($data)
    {
        $dataMap = array(
            '1' => '参数缺失',
            '2' => '参数错误',
            '0' => '请求成功',
            '-1' => '执行错误',
            '-10' => '无结果'
        );
        if (!isset($data['msg'])) {
            $data['msg'] = $dataMap[$data['code']];
        }
        return $data;
    }

    public $start_time; //开始时间
    public $end_time;   //结束时间
    public $r_end_time; //真正结束时间 为输入的结束时间 -1天
    public $user_id;
    public $goods_id;
    public $brand_id;
    public $cat_id;
    public $andWhere;   //查询的筛选条件 为键值对格式
    public $param;  //接收参数 TODO post 和 get 合并


    /**
     * 数据筛选条件
     */
    private function getParamData()
    {
        if(Yii::$app->request->isOptions) {
            return \GuzzleHttp\json_encode(['code' => 0]);
        }
        $post        = Yii::$app->request->post();
        $get         = Yii::$app->request->queryParams;
        $this->param = ArrayHelper::merge($post, $get);

        //指定时间内 --指定统计周期 指定用户 获取信息 --指定商品或者品牌品类
        if (!isset($this->param['start_time']) || !isset($this->param['end_time'])) {
            return $this->throwError(['code' => 1, 'param' => 'start_time or end_time']);
        }
        $this->start_time = $this->param['start_time'];
        $this->end_time   = $this->param['end_time'];
        if (!strtotime($this->start_time) || !strtotime($this->end_time)) {
            return $this->throwError(['code' => 2, 'param' => 'start_time or end_time']);
        }
        $this->start_time = date('Y-m-d', strtotime($this->start_time));
        $this->end_time   = date('Y-m-d', strtotime($this->end_time));
        if ($this->start_time > $this->end_time) {
            return $this->throwError(['code' => 2, 'msg' => '开始日期不能大于结束日期', 'param' => 'start_time']);
        }

        $this->r_end_time = $this->end_time;
        $this->end_time = date('Y-m-d', strtotime($this->end_time . '+1days'));
        //接收用户id
        if (!isset($this->param['user_id'])) {
            return $this->throwError(['code' => 1, 'param' => 'user_id']);
        }
        $this->user_id = $this->param['user_id'];
        if (!Users::findOne($this->user_id)) {
            return $this->throwError(['code' => 2, 'param' => 'user_id']);
        }

        //获取商品品类品牌--记录到andWhere里面
        !empty($this->param['goods_id']) && $this->goods_id = $this->param['goods_id'];
        !empty($this->param['cat_id']) && $this->cat_id = $this->param['cat_id'];
        !empty($this->param['brand_id']) && $this->brand_id = $this->param['brand_id'];
        $this->andWhere = '';
        if (empty($this->goods_id) && empty($this->brand_id) && empty($this->cat_id)) {
            // TODO 需求 这里是可选选项
            $this->andWhere = '-1';
        } else {
            if (isset($this->brand_id)) {
                //判断有效性
                $brand = Brand::find()->where([
                    'like' ,'brand_name' , $this->brand_id
                ])->one();
                if (!$brand) {
                    return $this->throwError(['code' => -10, 'param' => '品牌错误']);
                }
                $this->andWhere = ['brand_id' => $brand->brand_id];
            }
            if (isset($this->cat_id)) {
                $category = Category::find()->where([
                    'cat_name' => $this->cat_id
                ])->one();
                if (!$category) {
                    return $this->throwError(['code' => -10, 'param' => 'cat_id']);
                }
                $catList = [];
                CacheHelper::getCategoryLeavesByCatId($category->cat_id, $catList);
                $catList = array_column($catList, 'cat_id');
                $this->andWhere = ['cat_id' => $catList];
            }
            if (isset($this->goods_id)) {
                $goods = Goods::find()->where([
                    'like', 'goods_name', $this->goods_id
                ])->one();
                if (!$goods) {
                    return $this->throwError(['code' => -10, 'param' => '品牌错误']);
                }
                $this->andWhere = ['goods_id' => $goods->goods_id];
            }
        }
    }

    /**
     * 采购占比
     */
    private function getPurchaseProportion($startTime, $endTime)
    {
        //还是要指定日期内统计所有卖出商品的总金额
        $purchaseProportion = DataAnalysis::find()->where([
            'between', 'date', $startTime, $endTime
        ]);
        if ($this->andWhere != -1) {
            $purchaseProportion->andWhere($this->andWhere);
        }
        $purchaseProportion->asArray();
        $amount = $purchaseProportion->all();
        //取数据并且去重
        $userAmount = array();
        //一维数组 //去重
        foreach ($amount as $amountStr) {
            if (isset($userAmount[$amountStr['user_id']])) {
                $userAmount[$amountStr['user_id']] += $amountStr['goods_amount'];
                $userAmount[$amountStr['user_id']] = (string)$userAmount[$amountStr['user_id']];
            } else {
                $userAmount[$amountStr['user_id']] = $amountStr['goods_amount'];
                $userAmount[$amountStr['user_id']] = (string)$userAmount[$amountStr['user_id']];
            }
        }
        //对数组进行排序
        arsort($userAmount, SORT_DESC);
        $userAmount = array_values($userAmount);
        return ['goodsAmount' => $userAmount];
    }

    /**
     * 指定用户 指定日期内的 数据
     * --改为传入起止时间日期进行 2017/7/18
     */
    private function getUserData($startTime, $endTime)
    {
        $key       = 'cyclePurchaseData user:' . $this->user_id . ' time:' . $startTime . 'to' . $endTime . ' where'
            . serialize($this->andWhere);
        $redis     = Yii::$app->redis;
        $redisData = $redis->get($key);
        if ($redisData) {
            //redis中有数据
            $data = unserialize($redisData);
        } else {
            $purchase = DataAnalysis::find()->where([
                'user_id' => $this->user_id,
            ])->andWhere([
                'between', 'date', $startTime, $endTime
            ]);
            if ($this->andWhere != -1) {
                $purchase->andWhere($this->andWhere);
            }
            $purchase->asArray();
            $_purchase = clone $purchase;
            if (!$_purchase->all()) {
                $amount   = 0;
                $number   = 0;
                $orderNum = 0;
                $brandNum = 0;
                $skuNum   = 0;
            } else {
                $_amount   = clone $purchase;            //采购金额
                $amount    = $_amount->sum('goods_amount');
                $_number   = clone $purchase;             //采购数量
                $number    = $_number->sum('goods_number');
                $_orderNum = clone $purchase;           //订单数
                $orderNum  = count($_orderNum->select('group_id')->groupBy('group_id')->all());//$orderNum  = $_orderNum->select('group_id')->groupBy('group_id')->count();
                $_brandNum = clone $purchase;           //品牌数
                $brandNum  = count($_brandNum->select('brand_id')->groupBy('brand_id')->all());
                $_skuNum   = clone $purchase;     //sku数量
                $skuNum    = count($_skuNum->select('goods_id')->groupBy('goods_id')->all());
            }
            $data = [
                'amount' => (string)$amount,
                'number' => (string)$number,
                'orderNum' => (string)$orderNum,
                'brandNum' => (string)$brandNum,
                'skuNum' => (string)$skuNum,
            ];
            $redis->set($key, serialize($data));
        }
        return $data;
    }
}