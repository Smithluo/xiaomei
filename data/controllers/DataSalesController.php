<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/5 0005
 * Time: 9:21
 */

namespace data\controllers;

use common\helper\CacheHelper;
use common\models\Brand;
use common\models\Category;
use common\models\Goods;
use data\models\BrandAnalysis;
use data\models\DataAnalysis;
use data\models\SkuAnalysis;
use Yii;
use yii\helpers\ArrayHelper;

class DataSalesController extends DataBaseAuthController
{

    public $enableCsrfValidation = false;

    public function actionDownloadSalesList()
    {
        if (Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        $sales = $this->getSalesListData();
        //页面前面有输出内容会导致导出的文件乱码
        $data = \moonland\phpexcel\Excel::export([
            'format' => 'Excel5',
            'fileName' => '产品销售汇总列表',
            'models' => $sales,
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
        return $this->throwError(['code' => 0, 'data' => $data]);
    }

    public function actionGetSalesList()
    {
        if (Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        $sales = $this->getSalesListData();
        if (empty($sales)) {
            return $this->throwError(['code' => -10]);
        }
        if (isset($sales['code'])) {
            return $sales;
        }
        //分页
        $this->getParamPagination();
        $data = $this->getDataPagination($sales);
        return $this->throwError(['code' => 0, 'pageSum' => $this->pageSum, 'data' => $data]);
    }

    /**
     * @return array
     */
    private function getSalesListData()
    {
        $this->getParamData();
        //获取redis数据
        $key       = 'salesList time:' . $this->start_time . 'to' . $this->end_time . ' where:' . serialize($this->andWhere);
        $redis     = Yii::$app->redis;
        $redisData = $redis->get($key);
        if ($redisData) {
            $sales = unserialize($redisData);
        } else {
            $sales = DataAnalysis::find()->where([
                'between', 'date', $this->start_time, $this->r_end_time
            ]);
            if ($this->andWhere !== -1) {
                $sales->andWhere($this->andWhere);
            }
            $sales = $sales->asArray()->all();
            if (!$sales) {
                //处理 抛出无结果异常
                return $this->throwError(['code' => -10]);
            }
            $newSales = array();
            foreach ($sales as $salesStr) {
                if (isset($newSales[$salesStr['goods_id']])) {
                    $newSales[$salesStr['goods_id']]['goods_number'] += $salesStr['goods_number'];
                    $newSales[$salesStr['goods_id']]['goods_amount'] += $salesStr['goods_amount'];
                    if (!in_array($salesStr['group_id'], $newSales[$salesStr['goods_id']]['group_id'])) {
                        $newSales[$salesStr['goods_id']]['order_number'] += 1;
                        $newSales[$salesStr['goods_id']]['group_id'][] = $salesStr['group_id'];
                    }
                } else {
                    $newSales[$salesStr['goods_id']] = array(
                        'goods_id' => $salesStr['goods_id'],    //商品id
                        'goods_name' => (string)$salesStr['goods_name'],    //商品名称
                        'goods_sn' => (string)$salesStr['goods_sn'],    //条形码
                        'brand_id' => $salesStr['brand_id'],    //品牌id
                        'brand_name' => (string)$salesStr['brand_name'],    //品牌名称
                        'cat_id' => $salesStr['cat_id'],    //品类id
                        'cat_name' => (string)$salesStr['cat_name'],    //品类名称
                        'goods_amount' => (string)$salesStr['goods_amount'],    //销售金额
                        'goods_number' => (string)$salesStr['goods_number'],    //销售数量
                        'group_id' => array($salesStr['group_id']),    //总订单id，用于累加下单次数
                        'order_number' => 1 //下单次数
                    );
                }
            }
            //去重之后去键值
            $sales = array_values($newSales);

            //排序 TODO 先获取排序信息,作为sql查询的条件
            $this->getParamSort();
            //抽取一维数组
            $goodsAmount = array();
            $goodsNumber = array();
            $orderNumber = array();
            foreach ($sales as $salesStr) {
                $goodsAmount[] = $salesStr['goods_amount'];
                $goodsNumber[] = $salesStr['goods_number'];
                $orderNumber[] = $salesStr['order_number'];
            }
            $sortMap   = array(
                'goods_amount' => $goodsAmount,
                'goods_number' => $goodsNumber,
                'order_number' => $orderNumber,
            );
            $sortByMap = array(
                'desc' => SORT_DESC,
                'asc' => SORT_ASC
            );
            if (!isset($sortMap[$this->sort])) {
                return $this->throwError(['code' => 2, 'param' => 'sort']);
            }
            array_multisort($sortMap[$this->sort], $sortByMap[$this->sortBy], $sales);
            //--unset掉group_id 元素 TODO 在数组排序之前会导致两个goods_sn一样的信息搞混 另一个的信息会被前一个覆盖掉
            foreach ($sales as &$salesStr) {
                $salesStr['goods_amount'] = (string)number_format($salesStr['goods_amount'], 2);
                unset($salesStr['group_id']);
                unset($salesStr['goods_id']);
                unset($salesStr['brand_id']);
                unset($salesStr['cat_id']);
            }

            $redis->set($key, serialize($sales));
        }
        return $sales;
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
     * 排序信息
     */
    private function getParamSort()
    {
        if (!isset($this->param['sort'])) {
            return $this->throwError(['code' => 1, 'param' => 'sort']);
        }
        $this->sort   = $this->param['sort'];
        $this->sortBy = isset($this->param['sort_by']) ? $this->param['sort_by'] : 'desc';
    }

    public function actionGetSalesData()
    {
        if (Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        //指定日期范围内 销售金额 订单数 客户数 动销品牌数 品牌动销率 动销SKU SKU动销率
        $this->getParamData();  //获取输入的参数
        // 指定周期
        if (!isset($this->param['cycle'])) {
            return $this->throwError(['code' => 1, 'param' => 'cycle']);
        }
        $cycle    = $this->param['cycle'];
        $cycleMap = array(
            'day' => '1 day',
            'week' => '1 week',
            'month' => '1 month',
            'quarter' => '3 month',
            'years' => '1 years'
        );
        if (!isset($cycleMap[$cycle])) {
            //TODO 抛出异常
            return $this->throwError(['code' => 2, 'param' => 'cycle']);
        }
        $cycle = $cycleMap[$cycle];

        $key       = 'salesData time:' . $this->start_time . 'to' . $this->end_time . ' where:'
            . serialize($this->andWhere) . ' cycle:' . $cycle;
        $redis     = Yii::$app->redis;
        $redisData = $redis->get($key);
        if ($redisData) {
            //内存中存在该数据 直接取出来即可
            $data = unserialize($redisData);
        } else {
            //遍历时间
            $cycleDate = array();
            $nextDate  = date('Y-m-d', strtotime($this->start_time . '+' . $cycle));
            if ($nextDate > date('Y-m-d', strtotime($this->end_time))) {
                //TODO 这里是筛选周期无数据
                return $this->throwError(['code' => -10, 'msg' => '所选周期无数据']);
            }
            $data = array();
            for ($start = strtotime($this->start_time); $start < strtotime($this->end_time); $start += 60 * 60 * 24)  //按天遍历
            {
                //取时间日期
                $day = date('Y-m-d', $start);
                if ($day !== $nextDate) {
                    $cycleDate[] = $day;
                    //这个地方耗时最长
                    if (strtotime($day . '+ 1day') == strtotime($nextDate)) {
                        $cycleData          = $this->getCycleSalesData($cycleDate[0], end($cycleDate));
                        $data['date'][]     = $day;
                        $data['amount'][]   = $cycleData['amount'];
                        $data['number'][]   = $cycleData['number'];
                        $data['orderNum'][] = $cycleData['orderNum'];
                        $data['userNum'][]  = $cycleData['userNum'];
                        $data['brandNum'][] = $cycleData['brandNum'];
                        $data['skuNum'][]   = $cycleData['skuNum'];
                        $cycleDate          = array();
                        $nextDate           = date('Y-m-d', strtotime($nextDate . '+' . $cycle));
                    }
                    //TODO here
                }
            }

            //最后执行一次剩下的 作为单独的一个周期
            if (count($cycleDate) != 0) {
                $cycleData          = $this->getCycleSalesData($cycleDate[0], end($cycleDate));
                $data['date'][]     = end($cycleDate);
                $data['amount'][]   = $cycleData['amount'];
                $data['number'][]   = $cycleData['number'];
                $data['orderNum'][] = $cycleData['orderNum'];
                $data['userNum'][]  = $cycleData['userNum'];
                $data['brandNum'][] = $cycleData['brandNum'];
                $data['skuNum'][]   = $cycleData['skuNum'];
            }
            //总览
            $totalData = $this->getCycleSalesData($this->start_time, $this->r_end_time);
            //获取总品牌数量 -SKU数量
            $_dataBrand = BrandAnalysis::find()->where([
                'between', 'date', $this->start_time, $this->r_end_time
            ])->asArray()
                ->select(['brand_id'])
                ->groupBy('brand_id')
                ->all();
            $_dataSku   = SkuAnalysis::find()->where([
                'between', 'date', $this->start_time, $this->r_end_time
            ])->asArray()
                ->select('goods_id')
                ->groupBy('goods_id')
                ->all();
            if (empty($_dataBrand)) {
                $totalData['brand_proportion'] = (string)'--';
            } else {
                $brandNum                      = (string)count($_dataBrand);
                $totalData['brand_proportion'] = (string)round($totalData['brandNum'] / $brandNum * 100, 3) . '%';
            }
            if (empty($_dataSku)) {
                $totalData['sku_proportion']   = (string)'--';
            } else {
                //计算品牌和sku数量 -计算占比
                $skuNum = (string)count($_dataSku);
                $totalData['sku_proportion'] = (string)round($totalData['skuNum'] / $skuNum * 100, 3) . '%';
            }

            //占比 ，排名
            if ($this->andWhere == -1) {
                //TODO 在没有做筛选的时候，占比为该段时间销售总额除以自身
                $proportion = (string)'--';
                $rank       = (string)'--';
            } else {
                $salesProportion = $this->getSalesProportion($this->start_time, $this->r_end_time); //获取占比、排名数据
                $goodsSum = array_sum($salesProportion['goods_amount']);
                if ($goodsSum == 0) {
                    $proportion = '--';
                } else {
                    $proportion = round($totalData['amount'] / $goodsSum * 100, 3) . '%';
                }
                $rankNum         = array_search($totalData['amount'], $salesProportion['goods_amount']) + 1;
                $allNum          = count($salesProportion['goods_amount']);
                $rank            = $rankNum . '/' . $allNum;
                if ($totalData['amount'] == 0) {
                    $rank = '--';
                }
            }
            $totalData['sales_proportion'] = (string)$proportion;
            $totalData['sales_rank']       = (string)$rank;
            $totalData['amount']           = (string)number_format($totalData['amount'], 2);
            //TODO 这里先注释掉，后续需要进行大类跟大类排名
            if (isset($this->cat_id)) {
                $totalData['sales_rank'] = '--';
            }
            $data                          = [
                'totalData' => $totalData,
                'detailData' => $data
            ];
            $redis->set($key, serialize($data));
        }
        return $this->throwError(['code' => 0, 'data' => $data]);
    }

    /**
     *获取占比、排名数据
     */
    private function getSalesProportion($startTime, $endTime)
    {
        //指定时间内，统计所有卖出去的商品 以及金额
        $salesProportion = DataAnalysis::find()->where([
            'between', 'date', $startTime, $endTime
        ])->asArray()->all();

        //判断按什么筛选
        $name = '';
        if ($this->andWhere == -1) {
            //100%
            $name = -1;
        } else {
            if (!empty($this->brand_id)) {
                $name = 'brand_id';
            }
            if (!empty($this->cat_id)) {
                $name = 'cat_id';
            }
            if (!empty($this->goods_id)) {
                $name = 'goods_id';
            }
        }
        if ($name == -1) {
            //全部数据的 TODO 这里不会被执行
            //100%
            return;
        } else {
            $newSalesProportion = array();

            //一维数组
            foreach ($salesProportion as $salesProportionStr) {
                if (isset($newSalesProportion[$salesProportionStr[$name]])) {
                    $newSalesProportion[$salesProportionStr[$name]] += $salesProportionStr['goods_amount'];
                    $newSalesProportion[$salesProportionStr[$name]] = (string)$newSalesProportion[$salesProportionStr[$name]];
                } else {
                    $newSalesProportion[$salesProportionStr[$name]] = $salesProportionStr['goods_amount'];
                    $newSalesProportion[$salesProportionStr[$name]] = (string)$newSalesProportion[$salesProportionStr[$name]];
                }
            }
            arsort($newSalesProportion, SORT_DESC);
            $goodsAmount = array_values($newSalesProportion);

            return [
                'goods_amount' => $goodsAmount
            ];
        }

    }

    /**
     * 抛出错误 -用于接口返回数据
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
     * 获取参数信息
     */
    private function getParamData()
    {
        if(Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        $post        = Yii::$app->request->post();
        $get         = Yii::$app->request->queryParams;
        $this->param = ArrayHelper::merge($post, $get);

        //指定日期范围内
        if (!isset($this->param['start_time']) || !isset($this->param['end_time'])) {
            return $this->throwError(['code' => 1, 'param' => 'start_time or end_time']);
        }
        $this->start_time = $this->param['start_time'];
        $this->end_time   = $this->param['end_time'];
        if (!strtotime($this->start_time) || !strtotime($this->end_time)) {
            return $this->throwError(['code' => 2, 'msg' => '日期格式不正确', 'param' => 'start_time or end_time']);
        }
        $this->start_time = date('Y-m-d', strtotime($this->start_time));
        $this->end_time   = date('Y-m-d', strtotime($this->end_time));
        if ($this->start_time > $this->end_time) {
            return $this->throwError(['code' => 2, 'msg' => '开始日期不能大于结束日期', 'param' => 'start_time']);
        }
        $this->r_end_time = $this->end_time;
        $this->end_time = date('Y-m-d', strtotime($this->end_time . '+1days'));

        //获取商品品类品牌
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
     * @param $startTime
     * @param $endTime
     * @return array
     * 获取在指定时间内的销售数据统计概况
     */
    private function getCycleSalesData($startTime, $endTime)
    {
        $key       = 'cycleSalesData time:' . $startTime . 'to' . $endTime . ' where:' . serialize($this->andWhere);
        $redis     = Yii::$app->redis;
        $redisData = $redis->get($key);
        if ($redisData) {
            $data = unserialize($redisData);
        } else {
            $sales = DataAnalysis::find()->where([
                'between', 'date', $startTime, $endTime
            ]);
            if ($this->andWhere !== -1) {
                $sales->andWhere($this->andWhere);
            }
            $sales->asArray();
            $_sales = clone $sales;
            if (!$_sales->all()) {
                $amount   = 0;
                $number   = 0;
                $orderNum = 0;
                $userNum  = 0;
                $brandNum = 0;
                $skuNum   = 0;
            } else {
                //提取数据
                $_amount   = clone $sales;    //金额
                $amount    = $_amount->sum('goods_amount');
                $_number   = clone $sales;   //采购数量
                $number    = $_number->sum('goods_number');
                $_orderNum = clone $sales;   //订单数
                $orderNum  = count($_orderNum->select('group_id')->groupBy('group_id')->all());
                $_userNum  = clone $sales;   //客户数
                $userNum   = count($_userNum->select('user_id')->groupBy('user_id')->all());
                $_brandNum = clone $sales;  //动销品牌数
                $brandNum  = count($_brandNum->select('brand_id')->groupBy('brand_id')->all());
                $_skuNum   = clone $sales;    //动销SKU
                $skuNum    = count($_skuNum->select('goods_id')->groupBy('goods_id')->all());
            }
            $data = [
                'amount' => (string)$amount,
                'number' => (string)$number,
                'orderNum' => (string)$orderNum,
                'userNum' => (string)$userNum,
                'brandNum' => (string)$brandNum,
                'skuNum' => (string)$skuNum,
            ];
            $redis->set($key, serialize($data));
        }
        return $data;
    }

}