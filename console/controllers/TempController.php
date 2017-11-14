<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/28
 * Time: 15:15
 */

namespace console\controllers;

use common\helper\DateTimeHelper;
use common\helper\TextHelper;
use common\helper\FileHelper;

use common\models\ActivityManzeng;
use common\models\Ad;
use common\models\CouponRecord;
use common\models\Brand;
use common\models\CashRecord;
use common\models\Event;
use common\models\EventUserCount;
use common\models\Goods;
use common\models\GoodsActivity;
use common\models\OrderGoods;
use common\models\OrderInfo;
use common\models\Shipping;
use common\models\Spu;
use common\models\TouchAd;
use common\models\TouchArticle;
use common\models\Users;
use common\models\OrderGroup;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;


/**
 *  批量修改线上数据——临时处理，用过注释掉
 * Class TempController
 * @package console\controllers
 */
class TempController extends \yii\console\Controller
{

    /**
     * 批量修复 没有轮播图的商品， 通过与被复制商品的关系 复用图片
     */
    /*public function actionCopyImage()
    {
        $str = '1701=1176,1748=583,1721=812,1773=249,1711=843,1760=97,1735=74,1786=1318,1703=18,1749=724,1722=813,1774=250,1712=71,1761=120,1736=808,1787=536,1704=844,1750=1028,1723=805,1775=1023,1713=963,1762=182,1737=70,1788=656,1705=1178,1751=1030,1724=961,1776=1195,1714=790,1763=119,1738=802,1789=814,1706=796,1752=820,1725=811,1777=5,801=1800,1764=50,1739=803,1790=782,1707=789,1753=823,1726=1021,1778=248,1715=960,1765=95,1740=1029,1791=804,842=1804,1754=822,1727=113,1779=251,12=1799,1766=473,1741=962,1792=535,1708=797,1755=825,1728=454,1780=537,1716=791,1767=794,1742=824,1793=821,815=1803,1756=827,1729=810,1781=798,1717=959,1768=792,1743=174,1794=779,1709=958,1757=826,1730=72,1782=1314,1718=1375,1769=793,1744=723,1796=781,817=1802,1758=173,1731=770,1783=1315,1719=816,1770=455,1745=761,1797=663,818=1801,1759=172,1732=73,1784=1316,1022=1805,1771=795,1746=759,1798=788,1710=799,1807=99,1734=1031,1785=1317,1720=800,1772=809,1747=758';

        $arr = explode(',', $str);

        $i = 0;
        $j = 0;
        foreach ($arr as $item) {
            list($new, $old) = explode('=', $item);
            $goodsGallery = GoodsGallery::find()->where(['goods_id' => $old])->asArray()->all();
            if ($goodsGallery) {
                foreach ($goodsGallery as $item) {
                    $model = new GoodsGallery();
                    $model->goods_id = $new;

                    foreach ($item as $key => $value) {
                        if (!in_array($key, ['img_id', 'goods_id'])) {
                            $model->$key = $value;
                        }
                    }
                    $i++;
                    $model->save();
                    if ($model->errors) {
                        echo TextHelper::getErrorsMsg($model->errors);
                    }
                }
            }
            $j++;
        }
        echo $i.'张图片被复用 '.$j.'个商品添加了轮播图'.PHP_EOL;
    }*/

    /**
     * 历史订单 遍历到积分流水表中
     * 重复的order_id 不予写入
     */
    /*public function actionIntegral()
    {
        $query = PayLog::find()
            ->joinWith('orderInfo')
            ->where([
                'order_status' => OrderInfo::ORDER_STATUS_REALLY_DONE,
                'pay_status' => OrderInfo::PAY_STATUS_PAYED,
            ]);

        $sql = $query->createCommand()->getRawSql();
        $list = $query->all();

        if ($list) {
            echo $sql.PHP_EOL.'history order_info total count : '.count($list).PHP_EOL;
            $payIdCodeMap = Yii::$app->params['payIdCodeMap'];

            $i = 0;
            foreach ($list as $item) {
                //  更新历史的在后台手动支付的订单，pay_log 没有变更状态
                if ($item->is_paid == 0) {
                    $item->is_paid = 1;
                    $item->save();
                }
                $order = $item->orderInfo;
                $amount = bcsub($order['goods_amount'], $order['discount'], 2);
                $integral = floor(bcdiv($amount, 10));
                $data = [
                    'integral' => $integral,
                    'user_id' => $order['user_id'],
                    'pay_code' => $payIdCodeMap[$order['pay_id']],
                    'out_trade_no' => '历史订单',            //  暂时不处理历史订单的 第三方支付单号
                    'note' => (string)$order['order_id'],           //  单个支付存order_id,合并支付存group_id
                    'created_at' => $order['pay_time'],     //  用户支付时创建积分流水
                    'updated_at' => $order['recv_time'],    //  确认收货时 积分生效
                    'status' => Integral::STATUS_THAW,      //  历史积分直接生效
                ];

                $model = Integral::find()
                    ->where([
                        'user_id' => $order['user_id'],
                        'note' => $order['order_id'],
                    ])->one();
                if (!$model) {
                    $integral = new Integral();
                    $integral->setAttributes($data);
                    if (!$integral->save()) {
                        echo 'user_id:'.$order['user_id'].
                            ' order_id:'.$order['order_id'].
                            ' pay_log_id:'.$item->log_id.
                            ' integral record into mysql error'.
                            json_encode($integral->errors).'order_id:'.$order['order_id'].PHP_EOL;
                    } else {
                        $i++;
                    }
                } else {
                    echo 'user_id:'.$order['user_id'].
                        ' order_id:'.$order['order_id'].
                        ' pay_log_id:'.$item->log_id.
                        ' integral record exists'.PHP_EOL;
                }

            }
            echo $i.' integral record into mysql success'.PHP_EOL;
        } else {
            echo $sql.PHP_EOL;
        }
    }*/

    /**
     *  更新品牌的排序值——上线时执行一次就废掉
     *  原来的排序是0～100 正序
     *  新做的排序是0～255 逆序
     *  算法： 128 + (50 - 原来的排序值)
     */
    /*public function actionUpdateSortOrder()
    {
        //  品牌表 品牌信息有太多不完整的，不修改新后台的品牌信息校验规则，用原生的方式修改
        $sql = ' SELECT brand_id, sort_order FROM o_brand ';
        $brand_list = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($brand_list as $brand) {
            $sort_order = 128 + 50 - $brand['sort_order'];
            echo 'update o_brand.sort_order: '.$brand['sort_order'].' | '.$brand['sort_order'].' | '.$sort_order.PHP_EOL;
            $sql = ' UPDATE o_brand SET sort_order = '.$sort_order.' WHERE brand_id = '.$brand['brand_id'];
            Yii::$app->db->createCommand($sql)->execute();
        }

        //  PC站文章表
        $article_list = Article::find()->select(['article_id', 'sort_order'])->all();
        foreach ($article_list as $article) {
            $sort_order = 255 - $article->sort_order;
            echo 'update o_article.sort_order: '.$article->article_id.' | '.$article->sort_order.' | '.$sort_order.PHP_EOL;
            $article->sort_order = $sort_order;
            $article->save();
        }

        //  PC站文章分类表
        $article_cat_list = ArticleCat::find()->select(['cat_id', 'sort_order'])->all();
        foreach ($article_cat_list as $article_cat) {
            $sort_order = 128 + 50 - $article_cat->sort_order;
            echo 'update o_article_cat.sort_order: '.$article_cat->cat_id.' | '.$article_cat->sort_order.' | '.$sort_order.PHP_EOL;
            $article_cat->sort_order = $sort_order;
            $article_cat->save();
        }

        //  WeChat站文章分类表
        $article_cat_list = TouchArticleCat::find()->select(['cat_id', 'sort_order'])->all();
        foreach ($article_cat_list as $article_cat) {
            $sort_order = 255 - $article_cat->sort_order;
            echo 'update o_touch_article_cat.sort_order: '.$article_cat->cat_id.' | '.$article_cat->sort_order.' | '.$sort_order.PHP_EOL;

            $article_cat->sort_order = $sort_order;
            $article_cat->save();
        }

        //  商品分类表 o_category
        $category_list = Category::find()->select(['cat_id', 'sort_order'])->all();
        foreach ($category_list as $category) {
            $sort_order = 255 - $category->sort_order;
            echo 'update o_category.sort_order: '.$category->cat_id.' | '.$category->sort_order.' | '.$sort_order.PHP_EOL;
            $update_cat_sql = ' UPDATE o_category SET sort_order = '.$sort_order.' WHERE cat_id = '.$category->cat_id;
            Yii::$app->db->createCommand($update_cat_sql)->execute();
        }

        Goods::updateAll(['sort_order' => 30000], '');
        Attribute::updateAll(['sort_order' => 128], '');
    }*/
    /*//  WeChat站文章表 没有做排序
        $article_list = TouchArticle::find()->select(['article_id', 'sort_order'])->all();
        foreach ($article_list as $article) {
            $sort_order = 128 + $article->sort_order - 50;
            echo 'update o_article.sort_order: '.$article->article_id.' | '.$article->sort_order.' | '.$sort_order.PHP_EOL;
            $sql = ' UPDATE '.TouchArticle::tableName().' SET sort_order = '.$sort_order.
                ' WHERE article_id = '.$article->brand_id;
            $connect->createCommand($sql)->execute();
        }
        $connect->close();
        sleep(2);
        $connect = Yii::$app->db;*/

    /**
     * 把品牌中的 shipping_id 填充到 shipping_id 为0 的商品表中
     */
    public function actionGoodsShipping()
    {
        $goodsList = Goods::find()
            ->where([
                'shipping_id' => 0,
            ])->all();

        if ($goodsList) {
            $brandList = Brand::find()->select(['brand_id', 'shipping_id'])->asArray()->all();
            $brandShippingMap = array_column($brandList, 'shipping_id', 'brand_id');

            foreach ($goodsList as $goods) {
                if (isset($brandShippingMap[$goods->brand_id])) {
                    $shipping_id = (int)$brandShippingMap[$goods->brand_id];
                } else {
                    $shipping_id = Shipping::getDefaultShippingId();;
                }

                $goods->setAttribute('shipping_id', $shipping_id);
                if (!$goods->save()) {
                    echo $goods->goods_id.' -- save error '.TextHelper::getErrorsMsg($goods->errors).PHP_EOL;
                }
                echo $goods->goods_id.' -- '.$goods->shipping_id.PHP_EOL;
            }
        }
    }


    /**
     * 修的服务商作为业务员时的订单两次分成
     *
     * 服务商的cashRecord记录 note 值 '订单编号:order_sn列表'， 业务员的note 值为 单个order_sn
     * 查出 业务员身份的 分成在服务商身份中
     */
    public function actionCheckCachRecord()
    {
        $serviceCashRecord = CashRecord::find()
            ->select(['id', 'cash', 'user_id', 'note'])
            ->where([
                'like', 'note', '订单编号'
            ])->all();

        $orderSnMap = [];
        if (!empty($serviceCashRecord)) {
            foreach ($serviceCashRecord as $item) {
                $note = str_replace('订单编号：', '', $item->note);
                $orderSnList = explode(',', $note);

                if (empty($orderSnMap[$item->user_id])) {
                    $orderSnMap[$item->user_id] = $orderSnList;
                } else {
                    $orderSnMap[$item->user_id] = array_merge($orderSnMap[$item->user_id], $orderSnList);
                }
            }
        }

        $serviceMap = array_keys($orderSnMap);
        $serviceList = implode(',', $serviceMap);
        echo ' -- need solve serviceList :'.$serviceList.PHP_EOL.PHP_EOL;

        $errorCount = 0;
        if (!empty($orderSnMap)) {
            //  服务商作为业务员的分成 是重复的，应该删除
            $deleteSql = 'DELETE FROM '.CashRecord::tableName().' WHERE user_id IN ('.$serviceList.") AND cash >= 0 AND note NOT LIKE '订单编号%' ";
            echo $deleteSql;
            Yii::$app->db->createCommand($deleteSql)->execute();
            /*echo CashRecord::deleteAll([
                'and',
                ['user_id' => $serviceList],
                ['>', 'cash', 0],
                ['not like', 'note', '订单编号%'],
            ])->createCommand()->getRawSql();
            die();*/

            foreach ($orderSnMap as $userId => $item) {
                echo '--------------- userId = '.$userId.' update balance '.PHP_EOL;

                //  获取服务商的有效分成 修正banlance
                $cashRecord = CashRecord::find()
                    ->select(['id', 'cash', 'user_id', 'note', 'balance'])
                    ->where(['user_id' => $userId])
                    ->orderBy(['id' => SORT_ASC])
                    ->all();

                //  用多余分成的服务商 的正常流水 判定是否需要修正balance，用户看得到每一条流水的余额

                $i = 0;
                $nowBalance = 0;
                foreach ($cashRecord as $record) {
                    if ($i == 0) {
//                        $nowBalance = CashRecord::getBalanceById($userId, $record->id);
                        $nowBalance = $record->cash;
                    } else {
                        $nowBalance += $record->cash;   //  上次循环的 balance + 本次循环的 cash = 本次循环的 balance
                    }

                    if ($nowBalance < 0) {
                        echo ' reset balance when id = '.$record->id.' cash = '.$record->cash.' nowBalance = '.$nowBalance.PHP_EOL;
                        $nowBalance = 0;
                    }
                    //  避免服务商误解，当服务商提现后余额小于0 的把余额修正为0，保证后续的流水正确
                    $record->balance = $nowBalance;

                    if (!$record->save()) {
                        $errorCount++;
                        echo 'cashRecord.id = '.$record->id.' balance updated error: '.json_encode($record->errors).PHP_EOL;
                    } else {
                        echo 'cashRecord.id = '.$record->id.' balance updated success balance: '.$record->balance.PHP_EOL;
                    }

                    $i++;
                }
            }
        }

        echo  ' service errorCount : '.$errorCount.PHP_EOL;

        echo PHP_EOL.' -------------------------------- '.PHP_EOL;
        //  修正业务员的分成    收入，提取都要计算.
        $cashRecord = CashRecord::find()
            ->select(['id', 'cash', 'user_id', 'note'])
            ->where(['not like', 'note', '订单编号'])
            ->where(['not in', 'user_id', $serviceMap])
            ->orderBy([
                'user_id' => SORT_ASC,
                'id' => SORT_ASC
            ])->all();

        $errorCount = 0;
        $balance = [];
        if (!empty($cashRecord)) {
            foreach ($cashRecord as $item) {
                if (!isset($balance[$item->user_id])) {
                    $balance[$item->user_id] = $item->cash;
                } else {
                    $balance[$item->user_id] += $item->cash;
                }

                $item->balance = $balance[$item->user_id];
                if (!$item->save()) {
                    $errorCount++;
                    echo $item->id.' update error '.json_encode($item->errors).PHP_EOL;
                } else {
                    echo 'cashRecord.id = '.$item->id.' | cash : '.$item->cash.' | balance : '.$item->balance.PHP_EOL;
                }
            }
        }

        echo ' yewuyuan errorCount: '.$errorCount;

    }

    /**
     * 把历史订单中的一些信息摘到总单中
     */
    public function actionHandleOrderGroup() {
        foreach (OrderGroup::find()->with([
            'orders',
            'orders.servicerDivideRecord',
            'orders.wechatPayInfo',
            'orders.alipayInfo',
            'orders.yeePayInfo',
            'orders.yinlianPayInfo',
        ])->each(50) as $orderGroup) {
            $orderList = $orderGroup->orders;
            if (!empty($orderList)) {
                $firstOrder = $orderList[0];
                $orderGroup->consignee = $firstOrder->consignee;
                $orderGroup->country = $firstOrder->country;
                $orderGroup->province = $firstOrder->province;
                $orderGroup->city = $firstOrder->city;
                $orderGroup->district = $firstOrder->district;
                $orderGroup->address = $firstOrder->address;
                $orderGroup->mobile = $firstOrder->mobile;
                $orderGroup->pay_id = $firstOrder->pay_id;
                $orderGroup->pay_name = $firstOrder->pay_name;
                $orderGroup->pay_time = $firstOrder->pay_time;
            }

            $totalGoodsAmount = 0;
            $totalShippingFee = 0;
            $totalMoneyPaid = 0;
            $totalOrderAmount = 0;
            $totalDiscount = 0;
            $shippingTime = 0;
            $receiveTime = 0;

            //遍历子单，计算金额和发货时间
            foreach ($orderList as $orderInfo) {

                $wechatPayInfo = $orderInfo->wechatPayInfo;
                if (!empty($wechatPayInfo)) {
                    $wechatPayInfo->group_id = $orderGroup->group_id;
                    $wechatPayInfo->save(false);
                }

                $alipayInfo = $orderInfo->alipayInfo;
                if (!empty($alipayInfo)) {
                    $alipayInfo->group_id = $orderGroup->group_id;
                    $alipayInfo->save(false);
                }

                $yeepayInfo = $orderInfo->yeePayInfo;
                if (!empty($yeepayInfo)) {
                    $yeepayInfo->group_id = $orderGroup->group_id;
                    $yeepayInfo->save(false);
                }

                $yinlianPayInfo = $orderInfo->yinlianPayInfo;
                if (!empty($yinlianPayInfo)) {
                    $yinlianPayInfo->group_id = $orderGroup->group_id;
                    $yinlianPayInfo->save(false);
                }

                $totalGoodsAmount += $orderInfo->goods_amount;
                $totalShippingFee += $orderInfo->shipping_fee;
                $totalMoneyPaid += $orderInfo->money_paid;
                $totalOrderAmount += $orderInfo->order_amount;
                $totalDiscount += $orderInfo->discount;

                if ($orderInfo->shipping_time > $shippingTime) {
                    $shippingTime = $orderInfo->shipping_time;
                }

                if ($orderInfo->recv_time > $receiveTime) {
                    $receiveTime = $orderInfo->recv_time;
                }

                $servicerDivideRecords = $orderInfo->servicerDivideRecord;
                if (!empty($servicerDivideRecords)) {
                    foreach ($servicerDivideRecords as $divideRecord) {
                        $divideRecord->group_id = $orderGroup->group_id;
                        if (!$divideRecord->save()) {
                            $msg = 'divideRecord save fail id = '. $divideRecord->id. PHP_EOL;
                            echo $msg;
                        }
                    }
                }

                $deliveryOrderList = $orderInfo->deliveryOrder;

                foreach ($deliveryOrderList as $deliveryOrder) {
                    $deliveryOrder->group_id = $orderInfo->group_id;
                    $deliveryOrder->save();
                }

                $orderGoodsList = $orderInfo->ordergoods;
                //已全部发货的订单，把所有orderGoods的send_number纠正成goods_number
                if (in_array($orderInfo->order_status, [
                        OrderInfo::ORDER_STATUS_SPLITED,
                        OrderInfo::ORDER_STATUS_DONE,
                        OrderInfo::ORDER_STATUS_REALLY_DONE,
                    ])
                    && $orderInfo->pay_status == OrderInfo::PAY_STATUS_PAYED
                    && in_array($orderInfo->shipping_status, [
                        OrderInfo::SHIPPING_STATUS_SHIPPED,
                        OrderInfo::SHIPPING_STATUS_RECEIVED
                    ])) {
                    foreach ($orderGoodsList as $orderGoods) {
                        $orderGoods->send_number = $orderGoods->goods_number;
                        if (!$orderGoods->save()) {
                            $msg = 'orderGoods save error id = '. $orderGoods->rec_id. PHP_EOL;
                            echo $msg;
                        }
                        else {
                            echo 'orderGoods save 0 success id = '. $orderGoods->rec_id. PHP_EOL;
                        }
                    }
                }
                else {
                    $sendGoodsMap = [];
                    //先统计订单中商品的发货数量，按照id=>send_number的方式组织数据
                    foreach ($deliveryOrderList as $deliveryOrder) {
                        $deliveryGoodsList = $deliveryOrder->deliveryGoods;
                        foreach ($deliveryGoodsList as $deliveryGoods) {
                            if (!isset($sendGoodsMap[$deliveryGoods->goods_id])) {
                                $sendGoodsMap[$deliveryGoods->goods_id] = 0;
                            }
                            $sendGoodsMap[$deliveryGoods->goods_id] += $deliveryGoods->send_number;
                        }
                    }

                    //更新orderGoods中的已发货数量
                    foreach ($orderGoodsList as $orderGoods) {
                        //有赠品，发出的货物会多余当前商品的数量
                        if (!empty($sendGoodsMap[$orderGoods->goods_id])) {
                            $sendNumber = $sendGoodsMap[$orderGoods->goods_id];
                            $needSend = $orderGoods->goods_number - $orderGoods->send_number;
                            //如果够发货数量
                            if ($sendNumber >= $needSend) {
                                $orderGoods->send_number += $needSend;
                                $sendGoodsMap[$orderGoods->goods_id] -= $needSend;
                            }
                            //不够的话就全算在这个商品上了
                            else {
                                $orderGoods->send_number += $sendNumber;
                                $sendGoodsMap[$orderGoods->goods_id] = 0;
                            }
                            if (!$orderGoods->save()) {
                                $msg = 'orderGoods save error id = '. $orderGoods->rec_id. PHP_EOL;
                                echo $msg;
                            }
                            else {
                                echo 'orderGoods save 1 success id = '. $orderGoods->rec_id. PHP_EOL;
                            }
                        }
                    }
                }
            }

            $orderGroup->goods_amount = $totalGoodsAmount;
            $orderGroup->shipping_fee = $totalShippingFee;
            $orderGroup->money_paid = $totalMoneyPaid;
            $orderGroup->order_amount = $totalOrderAmount;
            $orderGroup->discount = $totalDiscount;

            $orderGroup->create_time = $orderInfo->add_time;
            $orderGroup->shipping_time = $orderInfo->shipping_time;
            $orderGroup->recv_time = $orderInfo->recv_time;

            //更新总单的综合状态
            $orderGroup->setupOrderStatus();

            if (!$orderGroup->save()) {
                $msg = 'save error id = '. $orderGroup->group_id. ', errors = '. VarDumper::export($orderGroup->errors);
                echo $msg;
                Yii::error($msg);
            }
            else {
                $msg = 'save success id = '. $orderGroup->group_id;
                Yii::warning($msg);
            }
        }
    }

    public function actionDivideOrderGroup($group_id) {
        if (empty($group_id)) {
            die('please enter group_id');
        }
        $orderGroup = OrderGroup::find()->where([
            'group_id' => $group_id,
        ])->one();

        if ($orderGroup->group_status == OrderGroup::ORDER_GROUP_STATUS_FINISHED) {
            $orderGroup->serviceDivide();
        }

        $orderGroup = OrderGroup::find()->where([
            'group_id' => $group_id,
        ])->one();

        foreach ($orderGroup->servicerDivideRecord as $record) {
            $cashRecord = CashRecord::createFromServicerDivideRecord($record);
            if (!empty($cashRecord)) {
                if ($cashRecord->save()) {
                    $record->money_in_record_id = $cashRecord->id;
                    if (!$record->save()) {
                        $cashRecord->delete();
                        Yii::error('关联流水失败 record = ' . VarDumper::export($record), __METHOD__);
                    }
                    echo '' . $record->group_id . ' divide cash in success' . PHP_EOL;
                } else {
                    Yii::warning('入账成功：cashRecord = ' . VarDumper::export($cashRecord) . ', record = ' . VarDumper::export($record), __METHOD__);
                    echo '' . $record->group_id . ' divide cash in fail' . PHP_EOL;
                }
            }
        }
    }

    /**
     * 给已经注册 并且审核过的用户 派发劵
     *
     * @param $eventId
     * @throws BadRequestHttpException
     */
    public function actionSendCoupon($eventId) {

        if (empty($eventId)) {
            throw new BadRequestHttpException('缺少活动ID', 1);
        }

        $userQuery = Users::find()->where([
            'is_checked' => Users::IS_CHECKED_STATUS_PASSED
        ]);

        $event = Event::find()->joinWith([
            'fullCutRule fullCutRule',
            'couponPkg couponPkg',
        ])->where([
            Event::tableName(). '.event_id' => $eventId
        ])->andWhere([
            'is_active' => 1,
        ])->andWhere([
            'event_type' => Event::EVENT_TYPE_COUPON,
        ])->andWhere([
            'couponPkg.enable' => 1,
        ])->one();

        foreach($userQuery->each(100) as $key => $user) {
            $preTime = time();

            if (empty($event)) {
                throw new BadRequestHttpException('缺少这个活动', 2);
            }
            else {
                Yii::trace('event = '. VarDumper::export($event), __METHOD__);
            }

            //查到这个用户参与这个活动的次数
            $eventUserCount = EventUserCount::find()->where([
                'event_id' => $eventId,
                'user_id' => $user->user_id,
            ])->one();

            //未参与过的用户就新建一个对象，为后面的入库做准备
            if (empty($eventUserCount)) {
                $eventUserCount = new EventUserCount();
                $eventUserCount->user_id = $user->user_id;
                $eventUserCount->event_id = $eventId;
                $eventUserCount->count = 0;
            }

            if ($eventUserCount->count >= $event->times_limit) {
                throw new BadRequestHttpException('已经超过参与次数', 3);
                Yii::warning('已经超过参与次数,用户:'.$user->mobile_phone);
                echo $user->mobile_phone.'已经参与过该活动，不能领取';
                continue;
            }

            $couponCanTake =[];

            foreach($event->fullCutRule as $rule) {
                $couponCanTake[] = CouponRecord::find()->where([
                    'user_id' =>0,
                    'rule_id'=>$rule->rule_id ,
                ])->one();
            }

            //领券，事务操作
            Event::getDb()->transaction(function ($db) use ($user, $couponCanTake, $eventUserCount, $preTime) {

                foreach ($couponCanTake as $rule) {

                    if (!empty($rule)) {
                        $rule->user_id = $user->user_id;
                        $rule->received_at = DateTimeHelper::gmtime();
                        $rule->save();
                        echo "success\n";
                    }
                    else {
                        throw new BadRequestHttpException('券已经被领完了', 4);
                    }
                }

                ++$eventUserCount->count;
                $eventUserCount->save();
            });
            $endTime = time();
            $spendTime = $endTime - $preTime ;
            echo $key."\n";
            echo 'wholeProcessTime:'.$spendTime."\n" ;
        }
    }

    public function actionSyncOrderGroup($group_id) {
        $orderGroup = OrderGroup::findOne([
            'group_id' => $group_id,
        ]);
        if (empty($orderGroup)) {
            echo 'order not found';
        }
        $orderGroup->setupOrderStatus();
        $orderGroup->syncFeeInfo();
        $orderGroup->syncTimeInfo();
        $orderGroup->save();
    }

    public function actionRecalcOrderAmount($group_id) {
        $orderGroup = OrderGroup::findOne([
            'group_id' => $group_id,
        ]);
        if (empty($orderGroup)) {
            echo 'order not found';
        }
        foreach ($orderGroup['orderList'] as $order) {
            $order->recalcGoodsAmount();
            $order->save();
        }

        $orderGroup->syncFeeInfo();
        $orderGroup->save();
    }

    public function actionCancelOrderGroup($group_id) {
        $orderGroup = OrderGroup::findOne([
            'group_id' => $group_id,
        ]);
        if (empty($orderGroup)) {
            echo 'order not found';
        }

        foreach ($orderGroup->orders as $order) {
            $order->order_status = OrderInfo::ORDER_STATUS_CANCELED;
            $order->pay_status = OrderInfo::PAY_STATUS_UNPAYED;
            $order->shipping_status = OrderInfo::SHIPPING_STATUS_UNSHIPPED;
            $order->save();
        }

        $orderGroup->setupOrderStatus();
        $orderGroup->save();
    }

    public function actionUnconfirmOrderGroup($group_id) {
        $orderGroup = OrderGroup::findOne([
            'group_id' => $group_id,
        ]);
        if (empty($orderGroup)) {
            echo 'order not found';
            return;
        }

        foreach ($orderGroup->orders as $order) {
            $order->order_status = OrderInfo::ORDER_STATUS_UNCONFIRMED;
            $order->pay_status = OrderInfo::PAY_STATUS_UNPAYED;
            $order->shipping_status = OrderInfo::SHIPPING_STATUS_UNSHIPPED;
            $order->save();
        }

        $orderGroup->setupOrderStatus();
        $orderGroup->save();
    }

    public function actionUnconfirmOrderInfo($order_sn) {
        $orderInfo = OrderInfo::find()->where([
            'order_sn' => $order_sn,
        ])->one();

        if (empty($orderInfo)) {
            echo 'order not found';
            return;
        }

        $orderInfo->order_status = OrderInfo::ORDER_STATUS_UNCONFIRMED;
        $orderInfo->pay_status = OrderInfo::PAY_STATUS_UNPAYED;
        $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_UNSHIPPED;
        $orderInfo->save();

        $orderGroup = $orderInfo->orderGroup;
        if (!empty($orderGroup)) {
            $orderGroup->setupOrderStatus();
            $orderGroup->save();
        }
    }

    public function actionReturnDoneOrderInfo($order_sn) {
        $orderInfo = OrderInfo::find()->where([
            'order_sn' => $order_sn,
        ])->one();

        if (empty($orderInfo)) {
            echo 'order not found';
            return;
        }

        $orderInfo->order_status = OrderInfo::ORDER_STATUS_RETURNED_DONE;
        $orderInfo->pay_status = OrderInfo::PAY_STATUS_UNPAYED;
        $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_UNSHIPPED;
        $orderInfo->save();

        $orderGroup = $orderInfo->orderGroup;
        if (!empty($orderGroup)) {
            $orderGroup->setupOrderStatus();
            $orderGroup->save();
        }
    }

    public function actionTrimBrandCountry() {
        $query = Brand::find()->where([
            'not',
            [
                'country' => null,
            ]
        ]);
        foreach ($query->each(50) as $brand) {
            $brand->country = trim($brand->country);
            $brand->save();
            echo 'success '.$brand->country. '%'.PHP_EOL;
        }
    }

    public function actionSyncManzengGoods() {
        $goodsList = \backend\models\Goods::getGiftGoodsMap();
        foreach ($goodsList as $goodsId => $goods) {
            $hasManzeng = ActivityManzeng::find()->where([
                'goods_id' => $goodsId,
            ])->exists();
            if (!$hasManzeng) {
                $manzeng = new ActivityManzeng();
                $manzeng->goods_id = $goodsId;
                $manzeng->is_show = 1;
                $manzeng->sort_order = 50;
                $manzeng->save();
            }
        }
    }

    /**
     * 修正 优惠券 使用时段
     *
     * 按活动 、规则 获取优惠券列表
     * 每组优惠券
     *
     *  没要设置有效时长的，使用活动的时段，
     *  有设置有效时长的，使用领取时间 —— 领取时间 + 时长
     *      如果领取时间 在19日之前，并且未使用的，修正为当前时间 到 当前时间 + 31天
     */
    public function actionModifyCouponUsePeriod()
    {
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '1G');

        $i = 0;
        $j = 0;
        $eventList = Event::find()
            ->joinWith(['fullCutRule'])
            ->where(['event_type' => Event::EVENT_TYPE_COUPON])
            ->all();


        foreach ($eventList as $event) {
            if (!empty($event->fullCutRule)) {

                foreach ($event->fullCutRule as $rule) {
                    //  按活动时间显示的优惠券 批量处理
                    if ($rule->term_of_validity == 0) {
                        CouponRecord::updateAll(
                            [
                                'start_time' => $event->start_time,
                                'end_time' => $event->end_time,
                            ],
                            ' event_id = '.$event->event_id.' AND rule_id = '.$rule->rule_id.' AND user_id > 0 '
                        );

                        CouponRecord::updateAll(
                            [
                                'start_time' => '0000-00-00 00:00:00',
                                'end_time' => '0000-00-00 00:00:00',
                            ],
                            [
                                'event_id' => $event->event_id,
                                'rule_id' => $rule->rule_id,
                                'user_id' => 0
                            ]
                        );
                        echo '['.$i++.']event_id = '.$event->event_id.'; rule_id = '.$rule->rule_id.' 的优惠券批量处理使用时段 '.PHP_EOL;
                    }
                    //  按领取时间修正优惠券的使用时段的，逐个处理
                    else {
                        $couponRecordList = CouponRecord::find()
                            ->where([
                                'event_id' => $event->event_id,
                                'rule_id' => $rule->rule_id,
                            ])->andWhere(['>', 'user_id', 0])
                            ->all();
                        if (!empty($couponRecordList)) {
                            foreach ($couponRecordList as $couponRecord) {
                                $startTimeStamp = DateTimeHelper::getFormatCNTimesTimestamp($couponRecord->received_at);
                                $endTimeStamp = $startTimeStamp + $rule->term_of_validity;

                                $startTime = date('Y-m-d H:i:s', $startTimeStamp);
                                $endTime = date('Y-m-d H:i:s', $endTimeStamp);

                                $couponRecord->setAttribute('start_time', $startTime);
                                $couponRecord->setAttribute('end_time', $endTime);

                                if (!$couponRecord->save()) {
                                    echo '['.$j++.'] $coupon->errors '.TextHelper::getErrorsMsg($couponRecord->errors).PHP_EOL;
                                } else {
                                    echo '['.$j++.'] $coupon->coupon_id = '.$couponRecord->coupon_id.
                                        '; event_id = '.$couponRecord->event_id.'; rule_id = '.$couponRecord->rule_id.
                                        ' set $startTime = '.$startTime.'; $endTime = '.$endTime.PHP_EOL;
                                }
                            }
                        }

                        CouponRecord::updateAll(
                            [
                                'start_time' => '0000-00-00 00:00:00',
                                'end_time' => '0000-00-00 00:00:00',
                            ],
                            [
                                'event_id' => $event->event_id,
                                'rule_id' => $rule->rule_id,
                                'user_id' => 0
                            ]
                        );
                    }
                }

            } else {
                if (!empty($event->event_id)) {
                    echo 'XXX event_id = '.$event->event_id.' 没有配置规则 '.PHP_EOL;
                } else {
                    echo ' ---- 未知错误 ---- '.PHP_EOL;
                }
            }
        }
    }

    public function actionLinkOrderGroupIdentity() {
        $query = OrderGroup::find()->with([
            'orders'
        ]);
        foreach ($query->each() as $orderGroup) {
            foreach ($orderGroup->orders as $order) {
                $order['group_identity'] = $orderGroup['id'];
                $order->save();
            }
        }
    }

    public function actionUpdateUserNameAsNickname()
    {
        $usersQuery = Users::find()->where([
            'nickname' => ''
        ])->andWhere([
            'not',
            [
                'mobile_phone' => '',
            ]
        ]);

        foreach($usersQuery->each(100) as $user) {
            if (empty($user->nickname)) {
                $user->nickname = mb_substr($user->user_name, 0, 19, 'utf-8');
                if($user->save()) {
                    echo $user->user_id.'更新成功'."\n";
                } else {
                    echo $user->user_id."\n";
                }
            }
        }
    }

    public function actionUpdateProvinceAndCity() {
        $query = Users::find()->joinWith([
            'defaultAddress'
        ])->where([
            'not',
            [
                'mobile_phone' => ''
            ],
        ])->andWhere([
            Users::tableName().'.province' => 0,
        ]);

        foreach ($query->each() as $user) {
            if (!empty($user['defaultAddress'])) {
                $defaultAddress = $user['defaultAddress'];
                if (empty($user->province)) {
                    $user->province = $defaultAddress['province'];
                }

                if (empty($user->city)) {
                    $user->city = $defaultAddress['city'];
                }

                if ($user->save()) {
                    echo ''. $user['user_id']. '操作成功'. PHP_EOL;
                }
                else {
                    echo ''. $user['user_id']. '操作失败'. PHP_EOL;
                }
            }
        }
    }

    /**
     * 修正没有pay_price 为0  的不正常的order_goods 记录
     */
    public function actionModifyPayPrice()
    {
        $orderGoods = OrderGoods::find()
            ->joinWith('orderInfo')
            ->where([
                'is_gift' => OrderGoods::IS_GIFT_NO,
                'pay_price' => 0.00
            ])->all();

        if (!empty($orderGoods)) {
            foreach ($orderGoods as $item) {
                if ($item->orderInfo->discount == 0) {
                    $item->setAttribute('pay_price', $item->goods_price);

                    if (!$item->save()) {
                        echo $item->rec_id.' 商品 修改失败 errors = '.TextHelper::getErrorsMsg($item->errors).PHP_EOL;
                    } else {
                        echo $item->rec_id.' 商品 修改成功'.PHP_EOL;
                    }
                } else {
                    echo ' order_id = '.$item->orderInfo->order_id.' 订单有问题 '.PHP_EOL;
                }
            }
        }
    }

    public function actionRandomArticleClick() {
        $query = TouchArticle::find()->where([
            'cat_id' => 24,
        ]);

        foreach ($query->each() as $article) {
            if ($article->click >= 100) {
                continue;
            }
            $random = rand(100, 300);
            $article->click = $random;
            $article->save();
        }
    }

    /**
     * 清理 debug 和 logs
     */
    public function actionDeleteLogsForTest()
    {
        //  m站 pc站的log
        FileHelper::clearDirectory(Yii::getAlias('@traceLog'));
        echo 'trace logs clear'.PHP_EOL;

        FileHelper::clearDirectory(Yii::getAlias('@backend').'/runtime/debug');
        FileHelper::clearDirectory(Yii::getAlias('@backend').'/runtime/logs');
        echo 'backend logs clear'.PHP_EOL;

        FileHelper::clearDirectory(Yii::getAlias('@market').'/runtime/debug');
        FileHelper::clearDirectory(Yii::getAlias('@market').'/runtime/logs');
        echo 'market logs clear'.PHP_EOL;

        FileHelper::clearDirectory(Yii::getAlias('@api').'/runtime/debug');
        FileHelper::clearDirectory(Yii::getAlias('@api').'/runtime/logs');
        echo 'api logs clear'.PHP_EOL;

        FileHelper::clearDirectory(Yii::getAlias('@service').'/runtime/debug');
        FileHelper::clearDirectory(Yii::getAlias('@service').'/runtime/logs');
        echo 'service logs clear'.PHP_EOL;

        FileHelper::clearDirectory(Yii::getAlias('@order').'/runtime/debug');
        FileHelper::clearDirectory(Yii::getAlias('@order').'/runtime/logs');
        echo 'order logs clear'.PHP_EOL.'OVER'.PHP_EOL;
    }

    /**
     * 修正 优惠券的领取时间
     * 有部分优惠券的领取时间取的是年份，修改为优惠券可使用时段的开始时间
     */
    /*public function actionModifyCouponReceivedAt()
    {
        $couponList = CouponRecord::find()
            ->where(['received_at' => 2017])
            ->all();

        foreach ($couponList as $coupon) {
            $coupon->received_at = DateTimeHelper::getFormatGMTTimesTimestamp($coupon->start_time);
            if ($coupon->save()) {
                echo 'success 优惠券 coupon_id = '.$coupon->coupon_id.' received_at = '.$coupon->received_at.PHP_EOL;
            } else {
                echo '!ERROR! 优惠券 coupon_id = '.$coupon->coupon_id.' received_at = '.$coupon->received_at.TextHelper::getErrorsMsg($coupon->errors).PHP_EOL;
            }
        }
    }*/

    //往订单中插入一个订单商品
    public function actionInsertOrderGoods($orderSn, $goodsId, $price, $number, $isGift, $parentId) {
        $order = OrderInfo::find()->where([
            'order_sn' => $orderSn,
        ])->one();

        if (empty($order)) {
            throw new BadRequestHttpException("找不到订单");
        }

        $goods = Goods::find()->where([
            'goods_id' => $goodsId,
        ])->one();

        if (empty($order)) {
            throw new BadRequestHttpException("找不到商品");
        }

        $orderGoods = OrderGoods::createFromGoods($goods);
        $orderGoods->goods_price = $price;
        $orderGoods->pay_price = $price;
        $orderGoods->goods_number = $number;
        $orderGoods->is_gift = $isGift;
        $orderGoods->parent_id = $parentId;

        $transaction = ActiveRecord::getDb()->beginTransaction();

        try {
            $order->link('ordergoods', $orderGoods);

            if ($orderGoods->hasErrors()) {
                throw new BadRequestHttpException("保存失败");
            }

            $order->recalcGoodsAmount();

            if (!$order->save()) {
                throw new \yii\console\Exception("订单保存失败");
            }

            $orderGroup = OrderGroup::find()->where([
                'group_id' => $order['group_id'],
            ])->one();

            if (empty($orderGroup)) {
                throw new \yii\console\Exception("总单找不到");
            }

            $orderGroup->syncFeeInfo();

            if (!$orderGroup->save()) {
                throw new \yii\console\Exception("总单保存失败");
            }

            $transaction->commit();

            echo "成功". PHP_EOL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    //更新订单商品的单价/数量，重新计算订单金额
    public function actionUpdateOrderGoods($recId, $price, $number) {
        $orderGoods = OrderGoods::find()->where([
            'rec_id' => $recId
        ])->one();
        if (empty($orderGoods)) {
            throw new \yii\console\Exception("订单商品找不到");
        }

        $orderGoods->goods_price = $price;
        $orderGoods->pay_price = $price;
        $orderGoods->goods_number = $number;

        $transaction = ActiveRecord::getDb()->beginTransaction();

        try {
            if (!$orderGoods->save()) {
                throw new BadRequestHttpException("订单商品保存失败");
            }

            $order = $orderGoods->orderInfo;
            $order->recalcGoodsAmount();

            if (!$order->save()) {
                throw new \yii\console\Exception("订单保存失败");
            }

            $orderGroup = OrderGroup::find()->where([
                'group_id' => $order['group_id'],
            ])->one();

            if (empty($orderGroup)) {
                throw new \yii\console\Exception("总单找不到");
            }

            $orderGroup->syncFeeInfo();

            if (!$orderGroup->save()) {
                throw new \yii\console\Exception("总单保存失败");
            }

            $transaction->commit();

            echo "成功". PHP_EOL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionUpdatePayPrice($recId, $price) {
        $orderGoods = OrderGoods::find()->where([
            'rec_id' => $recId
        ])->one();
        if (empty($orderGoods)) {
            throw new \yii\console\Exception("订单商品找不到");
        }

        $orderGoods->pay_price = $price;

        if (!$orderGoods->save()) {
            throw new BadRequestHttpException("订单商品保存失败");
        }

        echo 'success'. PHP_EOL;
    }

    public function actionDeleteOrderGoods($recId) {
        $orderGoods = OrderGoods::find()->joinWith('orderInfo orderInfo')->where([
            'rec_id' => $recId
        ])->one();
        if (empty($orderGoods)) {
            throw new \yii\console\Exception("订单商品找不到");
        }

        $orderId = $orderGoods['order_id'];

        $transaction = ActiveRecord::getDb()->beginTransaction();

        try {
            $orderGoods->delete();

            $order = OrderInfo::find()->where([
                'order_id' => $orderId,
            ])->one();

            $order->recalcGoodsAmount();

            if (!$order->save()) {
                throw new \yii\console\Exception("订单保存失败");
            }

            $orderGroup = OrderGroup::find()->where([
                'group_id' => $order['group_id'],
            ])->one();

            if (empty($orderGroup)) {
                throw new \yii\console\Exception("总单找不到");
            }

            $orderGroup->syncFeeInfo();

            if (!$orderGroup->save()) {
                throw new \yii\console\Exception("总单保存失败");
            }

            $transaction->commit();

            echo "成功". PHP_EOL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * 批量处理商品的前缀
     */
    public function actionUpdateGoodsPrefix()
    {
        $zfCount = Goods::updateAll(['prefix' => 'ZF'], ['like', 'goods_name', '直发']);
        echo 'modify ZF goods count = '.$zfCount.PHP_EOL;
        $xyCount = Goods::updateAll(['prefix' => 'XY'], ['like', 'goods_name', '非卖品小样']);
        echo 'modify XY goods count = '.$xyCount.PHP_EOL;
        $wlCount = Goods::updateAll(['prefix' => 'WL'], ['like', 'goods_name', '非卖品物料']);
        echo 'modify WL goods count = '.$wlCount.PHP_EOL;
        $jfCount = Goods::updateAll(['prefix' => 'JF'], ['like', 'goods_name', '积分兑换']);
        echo 'modify JF goods count = '.$jfCount.PHP_EOL;
    }

    /**
     * 修正礼包的价格信息
     */
    public function actionModifyGiftPkgPrice()
    {
        $giftPkgOrder = OrderInfo::find()
            ->joinWith([
                'orderGroup',
                'ordergoods'
            ])->where([OrderInfo::tableName().'.extension_code' => 'gift_pkg'])
            ->all();


        $GoodsPriceArray = [];
        if (!empty($giftPkgOrder)) {
            foreach ($giftPkgOrder as $order) {

                $goodsAmount = 0;
                $GoodsPriceArray = [];
                if (!empty($order->ordergoods)) {
                    foreach ($order->ordergoods as $goods) {
                        if (in_array($goods->goods_id, [2968, 2969, 2970])) {
                            OrderGoods::updateAll(
                                ['goods_price' => 23.00],
                                ['rec_id' => $goods->rec_id]
                            );
                            echo 'OrderGoods rec_id = '.$goods->rec_id.' update goods_price = 23.00'.PHP_EOL;
                            $goodsAmount += 23.00 * $goods->goods_number;
                            $GoodsPriceArray[$goods->rec_id] = 23.00;
                        } else {
                            $goodsAmount += $goods->goods_price * $goods->goods_number;
                            $GoodsPriceArray[$goods->rec_id] = $goods->goods_price;
                        }

                    }


                    $discount = $order->shipping_fee + $goodsAmount - ($order->order_amount + $order->money_paid);
                    OrderInfo::updateAll(
                        [
                            'goods_amount' => $goodsAmount,
                            'discount' => $discount,
                        ],
                        ['order_id' => $order->order_id]
                    );
                    echo 'OrderInfo order_id = '.$order->order_id.' update goods_amount = '.$goodsAmount.', discount = '.$discount.PHP_EOL;
                    $rate = 1 - $discount / $goodsAmount;
                    echo ' rate = '.$rate.PHP_EOL;
                    foreach ($GoodsPriceArray as $rec_id => $goods_price) {
                        $pay_price = $goods_price * $rate;
                        OrderGoods::updateAll(
                            ['pay_price' => $pay_price],
                            ['rec_id' => $rec_id]
                        );
                        echo 'OrderGoods rec_id = '.$rec_id.' update pay_price = '.$pay_price.PHP_EOL;
                    }

                    OrderGroup::updateAll(
                        [
                            'goods_amount' => $goodsAmount,
                            'discount' => $discount,
                        ],
                        ['group_id' => $order->group_id]
                    );
                    echo 'OrderGroup group_id = '.$order->group_id.' update goods_amount = '.$goodsAmount.' discount = '.$discount.PHP_EOL;
                }
                echo ' ================== line ================== '.PHP_EOL;
            }
        }
    }

    public function actionUpdateShippingInfo($orderSn, $shippingId, $shippingName, $shippingFee) {
        $orderInfo = OrderInfo::find()->where([
            'order_sn' => $orderSn,
        ])->one();

        if (empty($orderInfo)) {
            throw new BadRequestHttpException('未找到订单');
        }

        if ($orderInfo->updateShippingInfo($shippingId, $shippingName, $shippingFee)) {
            echo '成功'. PHP_EOL;
        } else {
            throw new ServerErrorHttpException('保存失败');
        }
    }

    public function actionClearDiscount($groupId) {
        $orderGroup = OrderGroup::find()->joinWith([
            'orderList orderList',
        ])->where([
            OrderGroup::tableName().'.group_id' => $groupId,
        ])->one();

        if (empty($orderGroup)) {
            throw new \yii\console\Exception('未找到订单');
        }

        $transaction = ActiveRecord::getDb()->beginTransaction();

        try {
            foreach ($orderGroup->orderList as $orderInfo) {
                $orderInfo->discount = 0;
                $orderInfo->recalcOrderAmount();
                if (!$orderInfo->save()) {
                    throw new \yii\console\Exception('保存订单失败 errors = '. VarDumper::dumpAsString($orderInfo->errors));
                }
            }

            $orderGroup->event_id = 0;
            $orderGroup->rule_id = 0;
            $orderGroup->syncFeeInfo();
            if (!$orderGroup->save()) {
                throw new \yii\console\Exception('保存总单失败 errors = '. VarDumper::dumpAsString($orderGroup->errors));
            }

            $transaction->commit();

            echo "成功". PHP_EOL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionTrimGoodsInfo(){
        $query = Goods::find();
        foreach ($query->each() as $goods) {
            if (strstr($goods->goods_sn, ' ')) {
                echo 'goods_id = '. $goods->goods_id. PHP_EOL;
                $goods->save();
            }
        }
        echo 'finished!'.PHP_EOL;
    }

    public function actionHandleGoodsContent() {
        $query = Goods::find();
        foreach ($query->each() as $goods) {
            if (!strstr($goods->goods_desc, 'width=100%')) {
                $goods->goods_desc = str_replace('<img ', '<img width=100% ', $goods->goods_desc);
                if ($goods->save()) {
                    echo 'success id = '. $goods->goods_id. PHP_EOL;
                }
                else {
                    echo 'fail id = '. $goods->goods_id. PHP_EOL;
                }
            } else {
                echo 'not handle id = '. $goods->goods_id. PHP_EOL;
            }
        }
    }

    public function actionCopyQty() {
        $query = Goods::find();
        foreach ($query->each() as $goods) {
            $goods->qty = $goods->number_per_box;
            $goods->save();
        }
    }

    public function actionCreateOrderInfo($groupId, $goodsId, $goodsPrice, $goodsNumber) {
        $orderGroup = OrderGroup::find()->where([
            'group_id' => $groupId,
        ])->one();
        if (empty($orderGroup)) {
            throw new \yii\console\Exception('找不到总单');
        }

        $goods = Goods::findOne($goodsId);
        if (empty($goods)) {
            throw new \yii\console\Exception('找不到商品');
        }

        $order = OrderInfo::createFromOrderGroup($orderGroup);
        $order->brand_id = $goods->brand_id;

        $orderGoods = OrderGoods::createFromGoods($goods);
        $orderGoods->goods_price = $goodsPrice;
        $orderGoods->pay_price = $goodsPrice;
        $orderGoods->goods_number = $goodsNumber;
        $orderGoods->is_gift = 0;
        $orderGoods->parent_id = 0;

        $transaction = ActiveRecord::getDb()->beginTransaction();

        try {
            $orderGroup->link('orderList', $order);

            $order->link('ordergoods', $orderGoods);

            if ($orderGoods->hasErrors()) {
                throw new BadRequestHttpException("保存失败");
            }

            $order->recalcGoodsAmount();

            if (!$order->save()) {
                throw new \yii\console\Exception("订单保存失败");
            }

            $orderGroup = OrderGroup::find()->where([
                'group_id' => $order['group_id'],
            ])->one();

            if (empty($orderGroup)) {
                throw new \yii\console\Exception("总单找不到");
            }

            $orderGroup->syncFeeInfo();

            if (!$orderGroup->save()) {
                throw new \yii\console\Exception("总单保存失败");
            }

            $transaction->commit();

            echo "成功". PHP_EOL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     *
     */
    public function actionImportSpuName()
    {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '1G');

        $spuNameArrStr = "英国Tangle Teezer 豪华便携美发梳 英国凯特王妃按摩顺发梳,韩国谜尚Missha魅力眼线笔,韩国2080 青龈茶牙膏,日本肌美精Kracie深层渗透面膜 ,德国施巴Sebamed温和洗发液 ,美国飞行宝宝Babiators2017年平光系列 经典飞行款0-3岁,韩国恩芝EUNJEE卫生巾 ,土耳其洛神诗Rosense 玫瑰Q10肌活身体乳霜,德国保黛宝Bettina Barty淡香水润系列沐浴露,韩国思亲肤SkinFood浆果面膜,韩国爱丝卡尔Esthaar护发素,日本悠斯晶Yuskin紫苏精华液体皂,法国梅翠诗MAITRE SAVON 马赛植物皂,日本奥丽肤olive果汁化妆水,韩国克丝可清Kuskuching 保加利亚玫瑰洗发液,意大利古驰Gucci同名经典男士淡香水EDT,澳洲Moxie超薄卫生巾,韩国谜尚Missha谜尚眼线笔 ,韩国2080 牙膏 ,日本凯朵KATE 畅妆眉笔 ,德国施巴Sebamed修护洗发露 ,美国飞行宝宝Babiators2017年平光系列 经典飞行款3-7岁,韩国恩芝EUNJEE卫生巾 纯棉日用,土耳其洛神诗Rosense 玫瑰肌活护手霜,德国保黛宝Bettina Barty淡香水润系列润肤乳,韩国思亲肤SkinFood巧克力香眉粉,韩国爱丝卡尔Esthaar洗发水,瑞典芮柔丝Kronosept 棉柔护翼卫生巾,法国柔舒然DOUCE NATURE阿甘油系列礼盒,日本奥丽肤olive紫草保湿霜,韩国克丝可清Kuskuching 活力护发素,意大利古驰Gucci竹韵魅力女士淡香精EDP,澳洲澳米莉亚Omeliae Lazy Gal懒菇凉水性可剥离指甲油,韩国谜尚Missha斯黛尔芭比娃娃睫毛膏,韩国2080 牙刷,日本凯朵KATE 黑白决色眼影盒 ,德国施巴Sebamed婴儿洁肤浴露 ,美国飞行宝宝Babiators2017年新款方框 平光 0-2岁 ,韩国恩芝EUNJEE卫生巾 纯棉夜用,土耳其洛神诗Rosense 玫瑰泡沫洗面奶,德国保黛宝Bettina Barty嫩滑沐浴啫喱 ,韩国薇欧薇VOV保湿修颜霜,韩国嫒蔻好Eco orient纯棉PLA卫生巾 ,台湾变脸猫Unicat黑面膜,法国柔舒然DOUCE NATURE阿甘油滋养套装,日本比那氏Propolinse 蜂胶漱口水,韩国克丝可清Kuskuching 活力沐浴露,意大利古驰Gucci罪爱男士淡香水,澳洲澳羊The Goat Skincare羊奶皂 ,韩国谜尚Missha斯黛尔便捷双色眉粉,韩国3R 牙刷,日本凯朵KATE 金烁悦彩眼影 ,德国施巴Sebamed婴儿泡泡沐浴露,美国飞行宝宝Babiators2017年新款方框 平光 3-5岁 ,韩国发希Fascy倍润手霜,西班牙MISTOL洗洁精,德国保黛宝Bettina Barty泡泡浴,韩国薇欧薇VOV活力皙颜气垫霜,韩国芭尚Bathsong皂,台湾菂桠Diovia 金盏花修护精华油 ,法国柔舒然DOUCE NATURE多功能马赛美肤皂,日本碧迪皙PDC浓润紧致黑色面膜 ,韩国克丝可清Kuskuching 活力洗发露,意大利古驰Gucci罪爱女士淡香水,澳洲澳芝曼G&M 鸸鹋精油霜,韩国谜尚Missha斯黛尔持色莹炫唇膏笔,韩国3R儿童牙膏,日本凯朵KATE 净容丝润粉饼,德国施巴Sebamed婴儿润肤乳 ,美国飞行宝宝BabiatorsAces王牌系列 (Ages 6岁以上),韩国菲诗小铺The Face Shop黑杆防水睫毛膏 ,西班牙蓓昂诗Byphasse卸妆湿巾,德国保黛宝Bettina Barty香水嫩白沐浴啫喱,韩国薇欧薇VOV慕斯水润修容气垫粉底液,韩国本恩BON纯棉加长型护垫,台湾菂桠Diovia 罗马洋甘菊纯露 ,法国柔舒然DOUCE NATURE精油美肤皂,日本碧迪皙PDC欣香洗面奶,韩国克丝可清Kuskuching 水润护发素,意大利玛莉吉亚Malizia棒棒糖香氛,澳洲澳芝曼G&M 绵羊油维生素e霜,韩国谜尚Missha斯黛尔轻滢亲肤粉饼SPF25/PA++,韩国3R儿童牙刷,日本凯朵KATE 均润矿物美肌散粉 ,德国雪本诗Schaebens 面膜,美国飞行宝宝Babiators偏光系列 0-3岁,韩国菲诗小铺The Face Shop洁面膏,西班牙斐格Prevex牙膏,德国保黛宝Bettina Barty香水嫩白润肤乳,韩国薇欧薇VOV牛仔睫毛膏,韩国本恩BON纯棉量多型日用卫生巾,台湾菂桠diovia 茉莉花纯露 （小花茉莉纯露）,法国柔舒然DOUCE NATURE马赛柔和滋润洗发沐浴二合一,日本缤若诗（曼丹） Bifesta 洁面湿纸巾,韩国克丝可清Kuskuching 水润沐浴露,意大利玛莉吉亚Malizia棒棒糖香水,澳洲澳芝曼G&M 柠檬茶树维生素e霜,韩国谜尚Missha斯黛尔轻滢亲肤控油蜜粉饼 ,韩国3R牙膏,日本凯朵KATE 美肌双色腮红,法国DOUCE NATURE柔舒然儿童牙膏,美国飞行宝宝Babiators偏光系列 2017年新款3-5,韩国菲诗小铺The Face Shop金盏花精华乳,西班牙美体舒bodynatur 私密护理洗液,德国波士（博斯）Boss玫瑰人生女士香水EDT,韩国薇欧薇VOV丝滑唇膏,韩国本恩BON纯棉日常型日用卫生巾,台湾菂桠Diovia 乳香赋活精华油,法国柔舒然DOUCE NATURE马赛液体皂,日本缤若诗（曼丹） Bifesta 美肌卸妆液,韩国克丝可清Kuskuching 水润洗发露,意大利玛莉吉亚Malizia沐浴露,澳洲澳芝曼G&M 维生素e保湿霜,韩国谜尚Missha斯黛尔轻滢亲肤蜜粉SPF15,韩国Inner感护理液,日本凯朵KATE 美妆净容散粉 ,法国贝儿娜Bella 长绒化妆棉,美国飞行宝宝Babiators偏光系列2017年新款 0-2,韩国花嫉Bloom season花嫉可可温和染发膏,西班牙欧洁士Oucasi 洗衣液,德国菲洛施Frosch 便器清洁剂,韩国薇欧薇VOV压缩蜜粉,韩国碧尓缇希BRTC嫩肌护肤精华水（神仙水）,台湾菂桠diovia 檀香纯露 ,法国柔舒然DOUCE NATURE摩洛哥坚果阿甘油 ,日本大王Megami卫生巾,韩国莱妃尔LAFFAIR三部曲面膜 ,意大利珮氏Pearls小番茄戶外香薰貼,澳洲澳芝曼G&M 维他命e修护霜,韩国谜尚Missha斯黛尔三色渐变眼影 ,韩国Inner感私密の洁一次性女性私密凝胶,日本凯朵KATE 美妆悦现粉底液,法国贝儿娜Bella经典双头化妆棉签,美国飞行宝宝Babiators偏光系列3-7岁,韩国花嫉Bloom season花嫉温和染发膏,西班牙葩蓓Babē润颜清痘,德国菲洛施Frosch 厨房重油污清洁剂,韩国薇欧薇VOV莹彩亮泽唇膏 SPF 8,韩国碧尓缇希BRTC维生素面膜,台湾菂桠Diovia 天竺葵调理精华油 ,法国柔舒然DOUCE NATURE清新沐浴液,日本芙丽芳丝freeplus保湿修护化妆水,韩国丽彩娜Richenna 泡沫式染发剂,意大利珮氏Pearls小番茄香薰喷雾,澳洲柏缇Pote 洗沐时尚,韩国谜尚Missha斯黛尔双头眼影眼线笔,韩国Inner感私密の润一次性女性私密凝胶,日本凯朵KATE 升级版畅妆持久眼线液 ,法国迪奥Dior花漾甜心女士淡香水EDT,美国津尔氏Thayers金缕梅爽肤水,韩国惑丽客Holika焕颜气垫修颜霜,香港谷芭KOOBA化妆棉  ,德国菲洛施Frosch 洗碗液,韩国薇欧薇VOV再见熊猫眼眉笔,韩国啵乐乐pororo儿童牙齿护理套盒 ,台湾菂桠Diovia 薰衣草纯露 ,法国柔舒然DOUCE NATURE舒缓雅致沐浴液,日本芙丽芳丝freeplus保湿修护迷你套装,韩国丽彩娜Richenna 容易快速染发膏,意大利尚护健SANTECARE健香薰喷雾,澳洲柏缇Pote 营养沐浴露,韩国谜尚Missha斯黛尔塑颜腮红,韩国Inner感一次性女性凝胶,日本凯朵KATE 升级版畅妆眼线液 ,法国迪奥Dior金色女郎真我(新款)粉色淡香水EDT,美国卡文克莱雷恩CK Free for Men自由男士淡香水EDT,韩国惑丽客Holika焕颜液体滚轴BB霜,香港谷芭KOOBA旅游小伴,德国菲洛施Frosch芦荟润肤洗衣液,韩国希希妮拉SEJELNINA面膜,韩国啵乐乐pororo儿童牙膏 ,台湾菂桠Diovia 岩兰草纯露 ,法国我的普罗旺斯MA PROVENCE 固体洁面皂,日本芙丽芳丝freeplus纯白美容液,韩国丽得姿LEADERS氨基酸面膜,意大利斯卡乐Scala 除垢剂,澳洲柏缇Pote 滋润洗发露,韩国谜尚Missha斯黛尔炫彩锁色润唇膏 ,韩国Lets Diet 升级款防晒帽,日本凯朵KATE 双效立体眉笔,法国迪奥Dior金色女郎真我女士淡香精EDP,美国卡文克莱雷恩CK ONE中性香水EDT,韩国惑丽客Holika面贴膜,香港谷芭KOOBA旅游小器皿,德国菲洛施Frosch清洗剂,韩国怡思美ESTHEMED面膜,韩国啵乐乐pororo儿童牙刷 卡通形象,台湾菂桠Diovia 印度玫瑰纯露 ,法国我的普罗旺斯MA PROVENCE 固体洗发皂 ,日本芙丽芳丝freeplus纯白凝皙化妆水,韩国丽得姿LEADERS领先水库面膜,意大利斯卡乐Scala 柑橘洗涤剂,澳洲比利山羊奶billie 保湿润肤乳,韩国谜尚Missha斯黛尔自动眉笔,韩国Lets Diet冰袖,日本凯朵KATE 细致浓色眼线笔  ,法国迪奥Dior金色女郎真我女士淡香水EDT,美国媚多M.A.D 精华,韩国惑丽客Holika睡眠面膜,香港谷芭KOOBA修眉刀,德国菲丝乐Facelle超薄日用卫生巾,韩国自然乐园Nature Republic精粹自然洁面乳,韩国啵乐乐pororo微笑儿童牙刷 ,台湾和草小屋Mixgreens 面膜 ,法国我的普罗旺斯MA PROVENCE 马赛皂 ,日本芙丽芳丝freeplus润肤喷雾,韩国丽得姿LEADERS修复面膜,意大利斯卡乐Scala 柠檬洗涤剂,澳洲比利山羊奶billie 护发素,韩国谜尚Missha斯黛尔自然美眉膏,韩国Lets diet儿童防晒帽,日本凯朵KATE 造型三色眉粉,法国迪奥Dior真我纯香香氛,美国尼克NakedN51面膜,韩国惑丽客Holika眼线笔,香港谷芭KOOBA纸轴多用途棉棒,德国菲丝乐Facelle超薄日用卫生巾加长,韩国自然乐园Nature Republic精粹自然面膜 ,韩国啵乐乐pororo幼儿牙膏 9无,台湾凯趣妮Catch you氨基酸刷具粉扑化妆刷专用清洗剂,法国香奈儿Chanel5号NO.5女士淡香精EDP,日本芙丽芳丝freeplus滋肤紧致弹力面膜,韩国丽得姿LEADERS椰果面膜,意大利斯卡乐Scala 浓缩洗衣液,澳洲比利山羊奶billie 沐浴液,韩国米时代白米香波,韩国Lets Diet呼吸防晒衣,日本凯朵KATE 棕影立体眼影盒,法国凡尔赛宫Versailles 漫步凡尔赛,美国确美同Coppertone水宝宝纯净防晒乳SPF30+PA+++ ,韩国珂莱欧Clio防水旋风眼线液,香港谷芭KOOBA专业粉扑 ,德国菲丝乐Facelle超薄夜用卫生巾加长,韩国自然乐园Nature Republic乳木果油滋润乳霜,韩国啵乐乐pororo幼儿啫喱牙膏10无,台湾康乃馨Carnation奈米核心健康护垫 ,法国香奈儿ChanelCOCO小姐女士淡香水EDT,日本芙丽芳丝freeplus滋肤紧致弹力面霜,韩国露娜LUNA露娜奇迹精华定妆喷雾套装,意大利斯卡乐Scala 清洁剂,澳洲比利山羊奶billie 洗发水,韩国米时代清系大米香皂,韩国Lets Diet经典款防晒帽,日本露姬婷Rosette洗面奶,法国凡尔赛宫Versailles 漫步凡尔赛（礼盒装）,美国丝华芙Suave 洗发水,韩国珂莱欧Clio精致纤细防水眼线笔,新西兰海丝蓓康Health Basics 丰泽营养洗发水,德国贺本清Herbacin小甘菊龟裂修护霜,韩国自然乐园Nature Republic手与自然护手霜  ,韩国婵真Charmzone美之学肌活洁面霜 ,台湾康乃馨Carnation御守棉超薄卫生棉 ,法国香奈儿Chanel粉色邂逅柔情淡香水,日本芙丽芳丝freeplus自然柔适粉饼 ,韩国露娜LUNA臻致保湿粉饼,意大利斯卡乐Scala 柔软剂-爱抚系列,澳洲蒂利Tilley 香熏油,韩国米时代去灰皂,韩国LG安宝笛保湿润体乳,日本美迪恩博士Dr.Medion 丝葩欧希保湿啫喱面膜,法国兰蔻Lancome真爱奇迹女士香水EDP,美国丝华芙Suave 滋润霜,韩国珂莱欧Clio精致纤细防水眼线液,新西兰海丝蓓康Health Basics 活力去屑洗发水,德国贺本清Herbacin小甘菊护手霜,韩国自然乐园Nature Republic手与自然免洗洗手液,韩国常绿秀手洗涤剂,台湾康倪Coni黑面膜,法国香奈儿Chanel黄色邂逅柔情淡香水,日本芙丽芳丝freeplus自然柔适粉底液 ,韩国吕Ryo滋养韧发密集莹韧洗发水清爽型,意大利斯卡乐Scala 衣物深层消毒剂,澳洲蒂利Tilley手工皂(香皂),韩国米时代柔系大米香皂,韩国LG安宝笛香水美肌沐浴露,日本美诗柯YOMIKO 花之物语系列,法国浪凡LANVIN光韵女士浓香水 ,美国夏依SummersEve女性专用洗液,韩国珂莱欧Clio魅棕持久两用眉笔,新西兰海丝蓓康Health Basics 芦荟沐浴乳,德国贺本清Herbacin小甘菊经典护手霜,韩国自然乐园Nature Republic自然主张面膜,韩国成美药妆ThemeLab成美面膜,台湾曼思水性可剥指甲油 ,法国香奈儿Chanel绿色邂逅柔情淡香水,日本福而可Fueki温柔滋润洗面霜,韩国吕Ryo滋养韧发密集莹韧洗发水滋润型,英国Tangle Teezer 豪华便携美发梳 英国凯特王妃按摩顺发梳,澳洲莉莉蜜丽Lilly&Milly 羊奶皂 ,韩国米时代润系大米香皂,韩国LG倍瑞傲派缤牙膏,日本美诗柯YOMIKO 可爱主义系列,法国浪凡LANVIN玫瑰传说香水 ,美国夏依SummersEve女用清洁湿巾,韩国珂莱欧Clio魔颜21隔离CC霜,新西兰海丝蓓康Health Basics 牛奶蜂蜜滋润沐浴乳,德国佳莉敏GLYSOMED礼盒装,美国Jason 草本牙膏,韩国成美药妆ThemeLab成美眼膜,台湾曼思指甲油套装（美甲）,法国香奈儿Chanel蔚蓝男士淡香水EDT,日本花王Kao蒸汽眼罩,韩国枚柯MAY COOP枫润细肤液 ,英国Tangle Teezer 尊贵流线美发梳 英国凯特王妃按摩顺发梳,澳洲山回Mt.retour按摩油,韩国那卡Larasoft蒸汽眼罩,韩国LG倍瑞傲全优倍护牙膏,日本娜丽丝Naris Up优物语化妆水,法国蕾娜LAINO 橄榄身体营养保湿乳,美国夏依SummersEve普通型洗液,韩国珂莱欧Clio欧丰盈睫毛膏（原名为珂莱欧沙龙睫毛膏）,新西兰海丝蓓康Health Basics 奇异果磨砂滋润沐浴乳,德国佳莉敏GLYSOMED乳木果护脚润腿霜,美国Jason 儿童牙膏,韩国春雨papa recipe面膜,台湾森田药妆Dr.Morita面膜 ,法国轩葶Naturaliste Bio 洗发水,日本花印HANAJIRUSHI精粹美白面膜,韩国美迪惠尔Mediheal 面膜,英国Tangle Teezer豪华便携美发梳,贝德玛BIODERMA舒妍洁肤液 ,韩国娜日舒Narsia超薄护垫,韩国LG贵爱娘6年红参中草药卫生巾,日本娜丽丝Naris Up悠纯柔肤沐浴乳,法国蕾娜LAINO 摩洛哥阿甘油,美国伊丽莎白雅顿Elizabeth Arden淡香水EDT,韩国珂莱欧Clio少女之吻润色唇膏,新西兰海丝蓓康Health Basics 蔷薇依兰沐浴乳,德国佳莉敏GLYSOMED洋甘菊护手霜,美国艾禾美ARM & HAMMER小苏打 ,韩国黛茉尔DERMAL 贝丽斯面膜,台湾我的心机My scheming 面膜,法国轩葶Naturaliste Bio 植物沐浴露,日本花印HANAJIRUSHI精粹美白面霜,韩国美迪惠尔Mediheal 潘士力面膜,英国碧缇丝Batiste 免水洗发喷雾,德国Sanosan哈罗闪儿童二合一洗发沐浴露,韩国娜日舒Narsia超薄日用卫生巾,韩国LG贵爱娘韩方中药卫生护垫,日本娜丽丝Naris Up悠纯柔顺洗发乳,法国蕾娜LAINO 沐浴露,美国伊丽莎白雅顿Elizabeth Arden第五大道女士淡香水EDT,韩国珂莱欧Clio少女之吻雾感唇膏,新西兰海丝蓓康Health Basics 清爽飘逸洗发水,德国佳莉敏GLYSOMED洋甘菊轻柔身体乳液,美国艾禾美ARM & HAMMER牙膏,韩国蒂佳婷Dr.Jart+ 面膜,台湾熊掌超人ECHAIN TECH 驱蚊贴片,法国轩葶Naturaliste Bio 植物皂,日本花印HANAJIRUSHI精粹美白身体乳,韩国美迪惠尔Mediheal黑炭面膜,英国博柏利Burberry动感节拍男士淡香水EDT,德国Sanosan哈罗闪婴儿二合一沐浴洗发露,韩国娜日舒Narsia超薄夜用卫生巾,韩国LG贵爱娘韩方中药卫生巾 ,日本娜诗丽NURSERY卸妆啫喱,法国蕾娜LAINO 舒缓柔肤身体乳,明色MEISHOKU海斗毛孔啫喱,韩国珂莱欧Clio无瑕水润粉饼,新西兰海丝蓓康Health Basics 柔顺修护洗发水,德国诺丽萝莉NONIQUE 焕彩奢华,美国爱森尔Episencial洗发沐浴露,韩国蒂佳婷Dr.Jart+ 男士洁面乳,台湾雪芙兰Cellina纯净洗发液,法国依云evian矿泉水喷雾,日本花印HANAJIRUSHI矿泉面膜,韩国美迪惠尔Mediheal恋朋面膜 ,英国博柏利Burberry动感节拍女士淡香精EDP,德国Sanosan哈罗闪婴儿柔润护肤乳,韩国娜日舒Narsia超长夜用卫生巾,韩国LG睿嫣润膏洗发水,日本牛牌Cow美肤香皂,法国蕾娜LAINO 洗发沐浴二合一旅行装,明色MEISHOKU润研遮瑕精华底霜,韩国珂莱欧Clio无暇魔力凝脂水润精华气垫粉底,新西兰海丝蓓康Health Basics 素馨木瓜沐浴乳,德国诺丽萝莉NONIQUE 热带风情莹润净肤霜,美国百蕾适Blistex 润唇膏 ,韩国蒂佳婷Dr.Jart+ 锁水保湿营养霜,台湾雪芙兰Cellina净肌矿物泥洗面乳,法国映芙EffiDerm映芙面霜,日本花印HANAJIRUSHI马油滋养修复面膜,韩国美诗蜜斯ms.miss CC霜,英国博柏利Burberry动感节拍女士淡香水EDT,德国Sanosan哈罗闪婴儿柔润护肤霜,韩国嫩姿NoTS平衡保湿乳液,韩国LLang红丽朗活力平衡喷雾,日本牛牌Cow牛牌美肤沐浴乳,法国蕾娜LAINO亮丽舒缓卸妆水,明色MEISHOKU双重弹力保湿化妆水,韩国珂莱欧Clio无暇魔力水润粉底液 ,新西兰海丝蓓康Health Basics 植物精华护发素,德国茜素斯Sans Soucis苹果精萃鲜颜焕肤霜,美国百蕾适Blistex 滋润护手霜,韩国蝶恩佳DOUBLE&ZERO黑面膜,台湾雪芙兰Cellina美肌清新沐浴乳,法国悠香伊Cottage绿茶沐浴乳,日本花印HANAJIRUSHI清新净颜卸妆水,韩国梦蜗WellDerma 精华面膜,英国博柏利Burberry伦敦男士淡香水EDT,德国Sanosan哈罗闪婴儿晚安护肤乳,韩国奇净客儿童洗衣皂 ,韩国SNP斯内普面膜 ,日本牛牌Cow牛乳石硷素材心洁面皂,法国丽芙 Le Comptoir沐浴液体马赛皂,明色MEISHOKU鲜果净化啫喱,韩国珂莱欧Clio艺彩魅力唇膏,意大利Aquafresh三色儿童牙膏,德国瑞铂希Repacell 肌源赋活精华素,美国佰蔻Belcam泡泡沐浴香波,韩国蝶恩佳DOUBLE&ZERO面膜,台湾雪芙兰Cellina美肌柔嫩沐浴乳,法国悠香伊Cottage新活焕颜去角质沐浴露,日本花印HANAJIRUSHI去角质美容皂,韩国梦蜗WellDerma 每周面膜,英国博柏利Burberry伦敦香氛,德国Sanosan哈罗闪婴儿晚安沐浴露,韩国奇净客儿童牙膏,韩国爱敬爱纪二十AGE 20精华粉底霜,日本牛牌Cow牛乳石硷自然派洁面皂,法国丽芙 泡泡浴,摩纳哥阿奎俪纳Akileine泡腾片,韩国珂莱欧Clio艺彩闪亮腮红,意大利Marvis牙膏薄荷复合味,德国瑞铂希Repacell 肌源抗皱奢华日夜面霜,美国达巴瓦拉DabbaWalla2016新款双肩包 ,韩国顿特斯特微细毛牙刷,台湾雪芙兰Cellina美肌舒缓沐浴乳,韩国2080 聪明宝宝牙刷,日本花印HANAJIRUSHI柔晰智慧粉底液 ,韩国梦蜗WellDerma 营养面膜,英国博柏利Burberry周末男士淡香水EDT,德国Sanosan哈罗闪婴儿滋润沐浴露,韩国瑞拉Diaforce迪雅芙丝眼膜 ,韩国爱敬可希丝KCS名画沐浴液,日本巧依丝Choice 氨基酸银耳洗洁精,法国俪肤缇Lift Argan天然阿甘护理油,日本sody 碳酸药片补充装,韩国克丝可清Kuskuching 安第斯印加果护发素,意大利Marvis牙膏薄荷味,德国瑞铂希Repacell 肌源密集修护小安瓶精华液,美国达巴瓦拉DabbaWalla2017年手提包 ,韩国顿特斯特牙医专业护理牙刷,台湾雪芙兰Cellina元素碳男性洁面乳FOR MEN,韩国2080 儿童牙膏,日本花印HANAJIRUSHI水漾美白焕肤面膜,韩国梦蜗WellDerma面膜 ,英国博柏利Burberry周末女士香水EDP,德国爱姬玛琳algemarin冰爽男士沐浴露,韩国施姈Vinciview-欣果 芦荟舒缓保湿凝胶,韩国爱可酷她纯净泡沫洗手液 敏感肌肤用,日本舒蔻（尤妮佳）Unicharm化妆棉,法国梅翠诗MAITRE SAVON 马赛洁肤皂,日本奥丽肤olive倍润护发素,韩国克丝可清Kuskuching 安第斯印加果沐浴露,意大利大公鸡头chante clair多能油污净,德国施巴Sebamed儿童洗发液 ,美国达巴瓦拉DabbaWalla经典款双肩包 ,韩国多多dodo红色恋人散粉,台湾雪芙兰Cellina元素碳男性沐浴乳FOR MEN,韩国2080 儿童牙膏特惠装,日本花印HANAJIRUSHI水漾润颜补水面膜,韩国谜尚Missha幻金凝彩净透修饰遮瑕笔SPF30+/PA++ ,英国蒂普莱丝Dimples杜碧丝脱毛泡沫,德国爱姬玛琳algemarin海洋清爽沐浴露,韩国施姈Vinciview誉清马油滋润面膜 ,韩国爱可酷她泡沫洗手液 ,日本苏菲SOFY温柔肌,法国梅翠诗MAITRE SAVON 马赛经典橄榄皂,日本奥丽肤olive倍润洗发水,韩国克丝可清Kuskuching 安第斯印加果洗发液,意大利范思哲Versace同名经典男士淡香水 EDT,德国施巴Sebamed洁肤沐浴露 ,美国达巴瓦拉DabbaWalla绝版款双肩包 ,韩国恩秀恩Aell su Aell儿湿纸巾婴儿（凸纹）,台湾雪芙兰Cellina滋养霜,韩国2080 贡白秘方牙膏,日本花印HANAJIRUSHI素肌感莹透无瑕修容霜 ,韩国谜尚Missha幻金凝彩雪肌粉饼SPF30+/PA+++,英国芳芯femfresh女性清洗液,德国爱姬玛琳algemarin经典香水沐浴露,韩国思亲肤SkinFood黑豆眉笔,韩国爱可酷她湿巾,日本悠斯晶Yuskin维生素乳霜,法国梅翠诗MAITRE SAVON 马赛经典棕榈皂,日本奥丽肤olive倍润浴液,韩国克丝可清Kuskuching 保加利亚玫瑰护发素,意大利古驰Gucci花之舞女士淡香精EDP,德国施巴Sebamed控油洗发露,美国达宝儿Dapple奶瓶清洗液 ,韩国恩芝EUNJEE安心棉卫生巾,泰国美舒MIISU 复方手工皂,韩国2080 乐活儿童牙膏,日本花印HANAJIRUSHI莹透保湿面膜,韩国谜尚Missha幻金凝彩至护霜 ,英国光芮Glo&Ray  唇爱微光唇膏,德国爱姬玛琳algemarin蜜桃香氛沐浴露,韩国思亲肤SkinFood坚果面膜,韩国爱蜜诗imyss天然面膜,日本悠斯晶Yuskin紫苏精华透明皂,法国梅翠诗MAITRE SAVON 马赛去角质皂,日本奥丽肤olive橄榄油,韩国克丝可清Kuskuching 保加利亚玫瑰沐浴露,意大利古驰Gucci花之舞女士淡香水EDT,德国施巴Sebamed去屑洗发露 ,美国达宝儿Dapple喷雾,韩国恩芝EUNJEE猫小菲纯棉卫生巾,土耳其洛神诗Rosense 大马士革玫瑰纯露,韩国2080 美白牙膏,日本肌美精Kracie立体浸透保湿面膜,韩国谜尚Missha美思金雪起润修颜膏SPF30+/PA++,英国光芮Glo&Ray 唇爱原色唇膏";

        $spuNameArr = explode(',', $spuNameArrStr);
        $spuNameArr = array_unique($spuNameArr);
        $spuNameArr = array_map('trim', $spuNameArr);
        $spuModel = new Spu();

        foreach ($spuNameArr as $spuName) {
            $spu = clone $spuModel;
            $spu->name = $spuName;
            if (!$spu->save()) {
                echo $spuName.' 导入失败'.PHP_EOL;
                continue;
            }
        }
    }

    public function actionReturnOrderGoodsStorage($groupId) {
        $orderGroup = OrderGroup::find()->with([
            'orderList',
            'orderList.ordergoods',
            'orderList.ordergoods.goods',
        ])->where([
            'group_id' => $groupId,
        ])->one();

        foreach ($orderGroup['orderList'] as $order) {
            foreach ($order['ordergoods'] as $ordergoods) {
                $goodsNumber = $ordergoods['goods_number'];
                $goods = $ordergoods['goods'];
                $goods['goods_number'] += $goodsNumber;
                $goods->save(false);
            }
        }
    }

    public function actionUpdateAd() {
        $query = TouchAd::find();
        foreach ($query->each() as $ad) {
            $ad->ad_code = str_replace('data/attached/ad_img/', '', $ad->ad_code);
            $ad->save(false);
        }
    }

    /**
     * 品牌绑定的event_id  用于用户主动领取优惠券
     */
    public function actionUpdateBrandEventReceiveType()
    {
        $brandModel = Brand::find()->select(['event_id'])->where(['>', 'event_id', 0])->all();

        $eventIdList = ArrayHelper::getColumn($brandModel, 'event_id');
        echo ' $eventIdList = '.json_encode($eventIdList).PHP_EOL;

        if (!empty($eventIdList)) {
            Event::updateAll(
                ['receive_type' => Event::RECEIVE_TYPE_DRAW],
                [
                    'event_type' => Event::EVENT_TYPE_COUPON,
                    'event_id' => $eventIdList
                ]
            );
        }
    }

    /**
     * 修正 2017年10月11日 团采商品下单未标记 extension_code = 'group_buy' 的 order_goods 记录
     */
    public function actionModifyGroupBuyGoodsInOrder() {
        $startTime = DateTimeHelper::getFormatGMTTimesTimestamp('2017-10-11 00:00:00');
        $endTime = DateTimeHelper::getFormatGMTTimesTimestamp('2017-10-11 23:59:59');

        //  当前生效的团采活动
        $aliveGroupBuyList = GoodsActivity::aliveGroupBuyList();
        $groupBuyGoodsList = array_keys($aliveGroupBuyList);

        $orderList = OrderInfo::find()
            ->select(['order_id'])
            ->where(['between', 'add_time', $startTime, $endTime])
            ->asArray()
            ->all();

        if (!empty($orderList)) {
            $orderIdList = array_column($orderList, 'order_id');
            OrderGoods::updateAll(
                ['extension_code' => 'group_buy'],
                [
                    'goods_id' => $groupBuyGoodsList,
                    'order_id' => $orderIdList
                ]
            );
        }
    }
}