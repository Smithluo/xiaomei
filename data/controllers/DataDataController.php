<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/12 0012
 * Time: 16:16
 */

namespace data\controllers;

use common\models\Brand;
use common\models\Goods;
use common\models\OrderGroup;
use data\models\BrandAnalysis;
use data\models\DataAnalysis;
use data\models\SkuAnalysis;
use yii\web\Controller;
use Yii;

class DataDataController extends Controller
{

//    public function actionGetData()
//    {
////        var_dump(DataBrand::find()->asArray()->all());
////        var_dump(DataSku::find()->asArray()->all());
////        echo Data::deleteAll();
//        var_dump(DataAnalysis::find()->limit(5)->asArray()->all());
//    }

    /**
     * 指定日期 --循环遍历订单 记录用户商品 订单信息
     *
     */
//    public function actionSetData()
//    {
//        $firstTime = time();
//        ini_set('max_execution_time', '0');
//        ini_set('memory_limit', '512M');
//        $start_time = "2016-01-01 00:00:01";
//        $end_time = "2017-08-26 11:00:00";
//        $date = '';
//        //echo date('Y-m-d H:i:s',strtotime('today-2days'));    //两天前零点的时间戳
//        for ($start = strtotime($start_time); $start <= strtotime($end_time); $start += 60 * 60 * 24)  //按天遍历
//        {
//            //对时间戳取整 --cron:today
//            $day = strtotime(date('Y-m-d', $start));
//            //查询
//            $order = OrderGroup::find()->alias('group')->joinWith([
//                'orders info',
//                'orders.ordergoods ordergoods',
//                'orders.ordergoods.goods goods',
//                'orders.ordergoods.goods.category cat',
//                'orders.ordergoods.goods.brand brand'
//            ])->where(
//                ['between', 'create_time', $day - 60 * 60 * 24, $day]
//            )->andWhere(['group_status' => OrderGroup::ORDER_GROUP_STATUS_FINISHED])
//                ->asArray()
//                ->groupBy('id')
//                ->all();
//            $goods = array();
//            foreach ($order as $orderStr) {
//                foreach ($orderStr['orders'] as $ordersStr) {
//                    foreach ($ordersStr['ordergoods'] as $ordergoodsStr) {
//                        $goodsStr = $ordergoodsStr['goods'];
//                        //记录订单信息
//                        $goods[] = array(
//                            'goods_id' => $goodsStr['goods_id'],    //商品id
//                            'goods_name' => isset($goodsStr['goods_name']) ? $goodsStr['goods_name'] : '',  //商品名称
//                            'goods_sn' => isset($goodsStr['goods_sn']) ? $goodsStr['goods_sn'] : '',    //商品条形码
//                            'goods_number' => $ordergoodsStr['goods_number'],  //销售数量
//                            'goods_amount' => $ordergoodsStr['pay_price'] * $ordergoodsStr['goods_number'], //销售金额
//                            'brand_id' => $goodsStr['brand_id'],    //品牌id
//                            'brand_name' => isset($goodsStr['brand']['brand_name']) ? $goodsStr['brand']['brand_name'] : '',//品牌名称
//                            'cat_id' => $goodsStr['cat_id'], //品类id
//                            'cat_name' => isset($goodsStr['category']['cat_name']) ? $goodsStr['category']['cat_name'] : '',  //品类名称
//                            'user_id' => $ordersStr['user_id'], //用户id
//                            'group_id' => $orderStr['group_id'],    //总订单号
//                            'consignee' => $ordersStr['consignee'],   //收货人
//                            'group_status' => OrderGroup::$order_group_status[$orderStr['group_status']],    //支付状态
//                            'order_amount' => $orderStr['goods_amount'],    //订单总货款
//                            'create_time' => date('Y-m-d H:i:s', $orderStr['create_time']), //下单时间
//                            'pay_time' => date('Y-m-d H:i:s', $orderStr['pay_time'])  //支付时间
//                        );
//                    }
//                }
//            }
//            //订单数据去重
//            $newGoods = array();
//            foreach ($goods as $key) {
//                $name = (string)$key['goods_id'] . (string)$key['user_id'] . (string)$key['group_id'];
//                if (isset($newGoods[$name])) {
//                    $newGoods[$name]['goods_number'] += $key['goods_number'];
//                    $newGoods[$name]['goods_amount'] += $key['goods_amount'];
//                } else {
//                    $newGoods[$name] = $key;
//                }
//            }
//            $goods = array_values($newGoods);
//            $date = date('Y-m-d', strtotime(date('Y-m-d', $day) . '-1days'));
//            $goodsRows = [];
//            foreach ($goods as $goodsStr) {
//                $goodsRows[] = array(
//                    $goodsStr['goods_id'],    //商品id
//                    $goodsStr['goods_name'],  //商品名称
//                    $goodsStr['goods_sn'],    //商品条形码
//                    $goodsStr['group_id'],    //商品总订单id
//                    $goodsStr['goods_number'],    //销售数量
//                    $goodsStr['goods_amount'],    //销售金额
//                    $goodsStr['cat_id'],    //品类id
//                    $goodsStr['cat_name'],  //品类名称
//                    $goodsStr['brand_id'],    //品牌id
//                    $goodsStr['brand_name'],  //品牌名称
//                    $goodsStr['user_id'],  //用户id
//                    $goodsStr['consignee'],  //收货人
//                    $goodsStr['group_status'], //支付状态
//                    $goodsStr['order_amount'],    //总订单金额
//                    $goodsStr['create_time'],  //下单时间
//                    $goodsStr['pay_time'],    //支付时间
//                    $date //登记时间
//                );
//            }
//            $result = Yii::$app->db->createCommand()->batchInsert('o_analysis_data',
//                ['goods_id', 'goods_name', 'goods_sn', 'group_id', 'goods_number', 'goods_amount', 'cat_id', 'cat_name',
//                    'brand_id', 'brand_name', 'user_id', 'consignee', 'group_status', 'order_amount', 'create_time', 'pay_time', 'date']
//                , $goodsRows
//            )->execute();
//            echo 'order saved , ' . $result . ' rows affected ' . ', date:' . $date . '<br>' . PHP_EOL;
//        }
//        $times = time() - $firstTime;
//        echo 'times:' . $times . ' s ';
//        echo $date = date('Y-m-d', strtotime(date('Y-m-d', time()) . '-1days'));
//        $brand = Brand::find()->select('brand_id')->where(['is_show' => Brand::IS_SHOW])->asArray()->groupBy('brand_id')->all();
//        $result = 0;
//        $brandRows = [];
//        foreach ($brand as $brandStr) {
//            $brandRows[] = [
//                $brandStr['brand_id'],
//                $date,
//            ];
//        }
//        if ($brandRows) {
//            $result = Yii::$app->db->createCommand()->batchInsert('o_analysis_brand', ['brand_id', 'date'], $brandRows)->execute();
//        }
//        echo 'brand saved , ' . $result . 'rows affected '. '<br>' . PHP_EOL;
//
//        $sku = Goods::find()->select('goods_id')->where([
//            'is_on_sale' => Goods::IS_ON_SALE,
//            'is_delete' => Goods::IS_NOT_DELETE
//        ])->asArray()->groupBy('goods_id')->all();
//        $skuRows = [];
//        $result = 0;
//        foreach ($sku as $skuStr) {
//            $skuRows[] = [
//                $skuStr['goods_id'],
//                $date,
//            ];
//        }
//        if ($skuRows) {
//            $result = Yii::$app->db->createCommand()->batchInsert('o_analysis_sku', ['goods_id', 'date'], $brandRows)->execute();
//        }
//        echo 'sku saved , ' . $result . 'rows affected ' . '<br>' . PHP_EOL;
//
//        echo 'finished!';
//    }
}