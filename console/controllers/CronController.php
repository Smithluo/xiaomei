<?php

namespace console\controllers;

use api\modules\v1\models\EventToGoods;
use api\modules\v1\models\Goods;
use api\modules\v1\models\GoodsAttr;
use backend\models\GoodsPkg;
use common\helper\SMSHelper;
use common\models\Article;
use common\models\CouponRecord;
use backend\models\EventRule;
use common\helper\CacheHelper;
use common\helper\FileHelper;
use common\models\DeliveryOrder;
use common\models\Event;
use common\helper\TextHelper;
use common\models\GoodsAction;
use common\models\GoodsLockStock;
use common\models\GoodsTag;
use common\models\Integral;
use common\models\OrderGoods;
use common\models\OrderGroup;
use common\models\Shipping;
use common\models\SmsIp;
use common\models\BrandDivideRecord;
use common\models\Tags;
use common\models\Users;
use common\models\VolumePrice;
use common\models\Brand;
use common\models\ServicerDivideRecord;
use common\models\OrderInfo;
use common\models\CashRecord;
use common\helper\DateTimeHelper;
use Yii;
use yii\db\Exception;
use \yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/23 0023
 * Time: 15:13
 */
class CronController extends \yii\console\Controller
{
    //一周
    const DURATION_WEEK = 604800;  //  7 * 24 * 60 * 60
    //一天
    const DURATION_DAY = 86400;  // 24 * 60 * 60

    /**
     * 定时修改订单状态
     */
    public function actionModifyOrderStatus() {
        echo __METHOD__. ' start '. PHP_EOL;

        $curTime = DateTimeHelper::getFormatCNTimesTimestamp();

        $cancelDuration = 2 * self::DURATION_DAY;
        $doneDuration = 2 * self::DURATION_WEEK;

        $orderIdList = [];  //  受影响的订单id列表
        $userIdList = [];   //  受影响的用户id列表

        //未付款的订单，超时后取消
        $orderQuery = OrderInfo::find()->where([
            'order_status' => OrderInfo::ORDER_STATUS_UNCONFIRMED,
        ])->andWhere([
            'pay_status' => OrderInfo::PAY_STATUS_UNPAYED,
        ])->andWhere([
            'shipping_status' => OrderInfo::SHIPPING_STATUS_UNSHIPPED,
        ]);

        foreach ($orderQuery->each() as $orderInfo) {
            $addTime = $orderInfo->add_time;
            $duration = $curTime - $addTime;

            //超过取消的间隔了
            if ($duration > $cancelDuration) {
                Yii::warning('取消订单 order_sn = '. $orderInfo->order_sn. ', group_id = '. $orderInfo->group_id, __METHOD__);
                $orderInfo->cancel('自动取消');
            }
        }

        //已发货的，超出时间后自动确认收货
        $orderQuery = OrderInfo::find()->where([
            'order_status' => OrderInfo::ORDER_STATUS_SPLITED,
            'pay_status' => OrderInfo::PAY_STATUS_PAYED,
            'shipping_status' => OrderInfo::SHIPPING_STATUS_SHIPPED,
        ]);

        foreach ($orderQuery->each() as $orderInfo) {
            $payTime = $orderInfo->shipping_time;
            $duration = $curTime - $payTime;

            if ($duration > $doneDuration) {

                $orderIdList[] = $orderInfo->order_id;
                $userIdList[] = $orderInfo->user_id;

                $orderInfo->recv_time = $curTime;
                $orderInfo->order_status = OrderInfo::ORDER_STATUS_REALLY_DONE;
                $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_RECEIVED;
                $orderInfo->note = '超时自动收货';
                $orderInfo->save();

                $orderGroup = $orderInfo->orderGroup;
                if (!empty($orderGroup)) {
                    $orderGroup->setupOrderStatus();
                    $orderGroup->save();
                }

                Yii::warning('已发货的自动收货 order_sn = '. $orderInfo->order_sn. ', group_id = '. $orderInfo->group_id, __METHOD__);
            }
        }

        //已完成，未变成真实完成的
        $orderQuery = OrderInfo::find()->where([
            'order_status' => OrderInfo::ORDER_STATUS_DONE,
            'pay_status' => OrderInfo::PAY_STATUS_PAYED,
            'shipping_status' => OrderInfo::SHIPPING_STATUS_RECEIVED,
        ]);

        foreach ($orderQuery->each() as $orderInfo) {

            $orderIdList[] = $orderInfo->order_id;
            $userIdList[] = $orderInfo->user_id;

            $orderInfo->recv_time = $curTime;
            $orderInfo->order_status = OrderInfo::ORDER_STATUS_REALLY_DONE;
            $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_RECEIVED;
            $orderInfo->note = '超时自动完成';
            $orderInfo->save();

            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }
            Yii::warning('已完成的自动完成 order_sn = '. $orderInfo->order_sn. ', group_id = '. $orderInfo->group_id, __METHOD__);
        }

        //遍历总单，分成
        foreach (OrderGroup::find()->with([
            'orders',
            'users',
            'users.servicerUser',
            'users.servicerUser.supserServicerUser',
        ])->where([
            '>',
            'create_time',
            1489651200,         //17年3月17日之后使用总单分成
        ])->andWhere([
            'group_status' => OrderGroup::ORDER_GROUP_STATUS_FINISHED,
        ])->each(50) as $orderGroup) {
            $orderGroup->setupOrderStatus();
            $orderGroup->save();

            //计算服务商分成，生成分成流水
            $orderGroup->serviceDivide();
        }

        //入账到服务商用户的钱包
        foreach (OrderGroup::find()->with([
            'servicerDivideRecord',
        ])->where([
            '>',
            'create_time',
            1489651200,         //17年3月17日之后使用总单分成
        ])->andWhere([
            'group_status' => OrderGroup::ORDER_GROUP_STATUS_FINISHED,
        ])->each(50) as $orderGroup) {
            foreach ($orderGroup->servicerDivideRecord as $record) {

                $salemanCashRecord = CashRecord::createSalemanCashRecord($record);
                if (!empty($salemanCashRecord)) {
                    if ($salemanCashRecord->save()) {
                        Yii::warning('业务员入账成功');
                        echo '' . $record->group_id . ' saleman divide cash in success' . PHP_EOL;
                    } else {
                        Yii::warning('业务员入账失败');
                        echo '' . $record->group_id . ' saleman divide cash in fail' . PHP_EOL;
                    }
                }
                else {
                    echo ''. $record->group_id. ' saleman divide null'. PHP_EOL;
                }

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
                        Yii::warning('入账失败：cashRecord = ' . VarDumper::export($cashRecord) . ', record = ' . VarDumper::export($record), __METHOD__);
                        echo '' . $record->group_id . ' divide cash in fail' . PHP_EOL;
                    }
                }
                
            }
        }

        //  已真实完成的订单要把积分状态置为可用      （暂时不做修正积分数值）
        if ($orderIdList) {
            Integral::updateAll(
                ['status' => Integral::STATUS_THAW],
                [
                    'note' => $orderIdList,
                    'status' => Integral::STATUS_FREEZE,
                ]
            );

            //  用户的积分状态有变更时，修改用户积分余额为0，用户访问到积分相关的页面时计算并修改正用户的积分余额
            $userIdList = array_unique($userIdList);
            Users::updateAll(
                ['int_balance' => 0],
                ['user_id' => $userIdList]
            );
        }
    }

    /**
     * 服务商 真实已完成订单分成记录 全部提取到钱包
     */
    public function actionServicerCashAll() {
        echo __FUNCTION__.'servicer order complete over  cash money start'.PHP_EOL;
        Yii::warning(__FUNCTION__.'servicer order complete over  cash money start');
        $query = new Query();
        $order_info_table = OrderInfo::tableName();
        //  查到所有的一级服务商    servicer_super_id = 0 或 servicer_super_id = servicer_user_id = user_id
        $servicers = $query->select('user_id')
            ->from(Users::tableName())
            ->where(['>', 'servicer_info_id', 0])
            ->andWhere([
                'OR',
                ['servicer_super_id' => 0],
                'servicer_super_id = user_id AND user_id = servicer_user_id'
            ])->orWhere(['user_id' => 1150])    //  小美服务商 servicer_super_id = servicer_user_id = 0 接收 没有服务商的用户的分成
            ->all();

        foreach($servicers as $servicer) {
            Yii::warning('servicer user_id = '.$servicer['user_id']);
            echo 'servicer user_id = '.$servicer['user_id'].PHP_EOL;
            $query = ServicerDivideRecord::find();
            //所有没有入账到钱包并且已经真正完成的订单
            $divideRecordsQuery = $query->select('*')
                ->leftJoin($order_info_table, ServicerDivideRecord::tableName().'.order_id='.$order_info_table.'.order_id')
                ->where('money_in_record_id=0')
                ->andWhere(['parent_servicer_user_id'=>$servicer['user_id']])
                ->andWhere([
                    $order_info_table.'.order_status'=>OrderInfo::ORDER_STATUS_REALLY_DONE,
                    $order_info_table.'.pay_status'=>OrderInfo::PAY_STATUS_PAYED,
                    $order_info_table.'.shipping_status'=>OrderInfo::SHIPPING_STATUS_RECEIVED
                ])
                ->andWhere([
                    '<=',
                    $order_info_table.'.add_time',
                    1489651200,
                ]);

            /*if ($servicer['user_id'] = 1150) {
                $divideRecordsForDebug = $divideRecordsQuery;
                echo $divideRecordsForDebug->createCommand()->getRawSql().PHP_EOL;
                die();
            }*/

            $divideRecords = $divideRecordsQuery->all();

            if(empty($divideRecords)) {
                continue;
            }

            $currCash = 0.0;
//            $currCashLevel2 = 0.0;
            $orderSn = '订单编号：';
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $keys = ['cash', 'user_id', 'note', 'created_time', 'balance'];
                $data = [];
                $isFirst = true;
                $ids = [];
                foreach($divideRecords as $divideRecord) {
                    $ids[] = $divideRecord->id;
                    $currCashLevel2 = $divideRecord->divide_amount;
                    $currCash += ($divideRecord->divide_amount + $divideRecord->parent_divide_amount);        //把二级服务商的余额也提取出来
                    if($isFirst) {
                        $orderSn .= $divideRecord->orderInfo->order_sn;
                        $isFirst = false;
                    }
                    else {
                        $orderSn .= ','. $divideRecord->orderInfo->order_sn;
                    }

                    //  当业务员不是服务商的时候才创建业务员的分成流水
                    if($divideRecord->servicer_user_id > 0 && $divideRecord->servicer_user_id != $servicer['user_id']) {
                        //  同一个服务商的分成记录 可能分给不同的服务商，所以 balance 的值要用数组来区分不同的业务员，
                        //  同一个业务员多条记录，就累加，保证在批量入库的时候数据准确
                        if (!isset($origTotalCash[$divideRecord->servicer_user_id])) {
                            $origTotalCash[$divideRecord->servicer_user_id] = CashRecord::totalCash($divideRecord->servicer_user_id);
                        }

                        $origTotalCash[$divideRecord->servicer_user_id] += $currCashLevel2;

                        Yii::warning('create CashRecord to servicer_user_id = '.$divideRecord->servicer_user_id.' $currCashLevel2 = '.$currCashLevel2);
                        echo 'create CashRecord to servicer_user_id = '.$divideRecord->servicer_user_id.' $currCashLevel2 = '.$currCashLevel2.PHP_EOL;
                        //给二级服务商批量创建流水
                        $data[] = [
                            0 => $currCashLevel2,
                            1 => $divideRecord->servicer_user_id,
                            2 => $divideRecord->orderInfo->order_sn,
                            3 => DateTimeHelper::getFormatGMTDateTime(time()),
                            4 => $origTotalCash[$divideRecord->servicer_user_id],
                        ];
                    }
                }

                if(count($data) > 0) {
                    Yii::$app->db->createCommand()->batchInsert(CashRecord::tableName(), $keys, $data)->execute();
                    Yii::warning('create CashRecord to  = '.$divideRecord->servicer_user_id.'  List success');
                    echo 'create CashRecord to  = '.$divideRecord->servicer_user_id.'  List success'.PHP_EOL;
                }
                $cashRecord = new CashRecord();
                $cashRecord->cash = $currCash;
                $cashRecord->user_id = $divideRecord->parent_servicer_user_id;
                $cashRecord->note = $orderSn;
                $cashRecord->created_time = DateTimeHelper::getFormatGMTDateTime(time());

                $totalCash = CashRecord::totalCash($divideRecord->parent_servicer_user_id);
                $totalCash = empty($totalCash) ? 0.00 : $totalCash;

                $cashRecord->balance = $totalCash + $currCash;

                Yii::warning('$cashRecord->cash = '.$currCash);
                echo '$cashRecord->cash = '.$currCash.PHP_EOL;
                if($cashRecord->validate()) {
                    $insertCashRecordSql = Yii::$app->db->createCommand()->insert(CashRecord::tableName(), [
                        'cash' => $cashRecord->cash,
                        'user_id' => $cashRecord->user_id,
                        'note' => $cashRecord->note,
                        'created_time' => $cashRecord->created_time,
                        'balance' => $cashRecord->balance,
                    ]);
                    $insertCashRecordSqlForDebug = $insertCashRecordSql;
                    Yii::warning('insert CashRecord SQL : '.$insertCashRecordSqlForDebug->getRawSql());
                    echo 'insert CashRecord SQL : '.$insertCashRecordSqlForDebug->getRawSql().PHP_EOL;

                    if($insertCashRecordSql->execute()) {
                        Yii::warning('CashRecord into MySQL success');
                        echo 'CashRecord into MySQL success';
                        $cashRecordId = Yii::$app->db->getLastInsertID();

                        Yii::$app->db->createCommand()->update(ServicerDivideRecord::tableName(), ['money_in_record_id'=>$cashRecordId], ['id' => $ids])->execute();
                        Yii::warning('ServicerDivideRecord change status; actionServicerCashAll success');
                        echo 'ServicerDivideRecord change status; actionServicerCashAll success'.PHP_EOL;

                        $transaction->commit();
                    }
                    continue;
                } else {
                    Yii::warning('$cashRecord validate error'.TextHelper::getErrorsMsg($cashRecord->errors));
                    echo '$cashRecord validate error'.TextHelper::getErrorsMsg($cashRecord->errors).PHP_EOL;
                }

                Yii::warning('orders actionServicerCashAll error, rollback1');
                echo 'orders actionServicerCashAll error, rollback1';
                $transaction->rollBack();
            } catch (Exception $e) {
                Yii::warning('$orderSn '.$divideRecord->orderInfo->order_sn.' orders actionServicerCashAll error, rollback2');
                echo '$orderSn '.$divideRecord->orderInfo->order_sn.' orders actionServicerCashAll error, rollback2'
                    .PHP_EOL;
                $transaction->rollBack();
            }
        }
        echo __FUNCTION__.'servicer order complete over  cash money end'.PHP_EOL;
        Yii::warning(__FUNCTION__.'servicer order complete over  cash money end');
    }

    /**
     * 品牌商 真实已完成订单分成记录 全部提取到钱包
     */
    public function actionBrandCashAll()
    {
        echo '品牌商 真实已完成订单分成记录 全部提取到钱包 开始';
        //  1、获取当前未提取到钱包的记录对应的订单号
        $brand_divide_record = BrandDivideRecord::findAll([
            'cash_record_id' => 0,
            'status' => BrandDivideRecord::BRAND_DIVIDE_RECORD_STATUS_UNTRACTED,
        ]);

        //  2、验证订单号对应订单状态是可提取状态
        foreach ($brand_divide_record as $record) {
            $order_info = $record->orderInfo;
            if ($order_info->order_status == OrderInfo::ORDER_STATUS_REALLY_DONE &&
                $order_info->shipping_status = OrderInfo::SHIPPING_STATUS_RECEIVED &&
                    $order_info->pay_status = OrderInfo::PAY_STATUS_PAYED
            ) {
                //  3、获取品牌商user_id

                $brand_user = Brand::find()->where(['brand_id' => $record->brand_id])->one();
                if ($brand_user && $brand_user->supplier_user_id) {
                    $brand_user_id = $brand_user->supplier_user_id;
                } else {
                    Yii::info('ID为'.$record->brand_id.'的品牌没有绑定品牌商，请尽快绑定', 2);
                    continue;
                }

                //  4、提取订单分成到钱包，修改分成状态为已提取,写入钱包入账记录id
                $connect = Yii::$app->db;
                $transaction = $connect->beginTransaction();
                try{
                    $cashRecord = new CashRecord();
                    $cashRecord->cash = $record->divide_amount;
                    $cashRecord->user_id = $brand_user_id;
                    $cashRecord->note = '订单编号：'.$order_info->order_sn;
                    $cashRecord->created_time = DateTimeHelper::getFormatGMTDateTime(time());
                    $cashRecord->balance = bcadd(CashRecord::totalCash($brand_user_id), $cashRecord->cash, 2);

                    $connect->createCommand()->insert('o_cash_record', [
                        'cash' => $cashRecord->cash,
                        'user_id' => $cashRecord->user_id,
                        'note' => $cashRecord->note,
                        'created_time' => $cashRecord->created_time,
                        'balance' => $cashRecord->balance,
                    ])->execute();
                    $cash_record_id = Yii::$app->db->getLastInsertID();

                    $connect->createCommand()->update('o_brand_divide_record', [
                        'cash_record_id' => $cash_record_id,
                        'status' => BrandDivideRecord::BRAND_DIVIDE_RECORD_STATUS_TRACTED,
                    ])->execute();

                    $transaction->commit();
                } catch (Exception $e) {
                    Yii::info('ID为'.$record->id.'的brand_divide_record记录提取到钱包失败');
                    $transaction->rollBack();
                }
            }
        }
        echo '品牌商 真实已完成订单分成记录 全部提取到钱包 结束';
    }

    /**
     * 清除会员价格
     * @throws \yii\db\Exception
     */
    public function actionDelMemberPrice()
    {
        /*echo '同步注册会员价格 开始';
        $sql1 = "UPDATE `o_member_price`
INNER JOIN `o_goods` ON `o_member_price`.`goods_id` = `o_goods`.`goods_id`
SET `o_member_price`.`user_price` = `o_goods`.`shop_price`";
        Yii::$app->db->createCommand($sql1)->execute();*/

        echo '清除注册会员以外的价格 开始';
        $sql_del = ' DELETE FROM o_member_price WHERE user_rank > 0 ';
        Yii::$app->db->createCommand($sql_del)->execute();

        /*echo '同步企业会员价格 开始';
        $sql2 = ' INSERT INTO o_member_price(goods_id, user_rank, user_price) SELECT goods_id,2, user_price FROM o_member_price WHERE user_rank = 1 ';
        Yii::$app->db->createCommand($sql2)->execute();

        echo '同步企业会员价格 开始';
        $sql3 = ' INSERT INTO o_member_price(goods_id, user_rank, user_price) SELECT goods_id,3, user_price FROM o_member_price  WHERE user_rank = 1 ';
        Yii::$app->db->createCommand($sql3)->execute();*/

        echo '同步会员价格 结束';
    }

    /**
     * 定时刷新当前有效的活动
     */
    public function actionRefreshActiveEvents()
    {
        $now = DateTimeHelper::getFormatGMTTimesTimestamp(time());
        $active_events = Event::find()->where([
            ['is_active' => Event::IS_ACTIVE],
            ['>', 'start_time', $now],
            ['<', 'end_time', $now]
        ])->orderBy([
            'event_id' => SORT_DESC
        ])->asArray()
            ->all();

        $params = Yii::$app->params;
        $cache_file_name = $params['caches_base_dir'].$params['cache_file_name']['active_events'];
        if ($active_events) {
            foreach ($active_events as &$event) {

                $event_rule = EventRule::find()->select([
                    'rule_name', 'match', 'gift_id', 'gift_num', 'gift_show_peice', 'gift_need_pay'
                ])->where(['rule_id' => $event['rule_id']])
                    ->asArray()
                    ->one();
                //  如果赠品已下架、已删除或剩余数量不满足赠送的最小数量，则活动无效
                if ($event_rule) {
                    $goods = Goods::find()->select('goods_id')
                        ->where([
                            'goods_id' => $event_rule->gift_id,
                            'is_on_sale' => Goods::IS_ON_SALE,
                            'is_delete' => Goods::IS_NOT_DELETE
                        ])->andWhere([
                            '>=', 'goods_number', $event_rule->gift_num
                        ])->one();
                    if ($goods) {
                        $event['event_rule'] = $event_rule;
                    }
                }
                if (!isset($event['event_rule'])) {
                    unset($event);
                    continue;
                }

                $pkg = GoodsPkg::findOne(['pkg_id' => $event['pkg_id']]);
                //  优先判断 allow_goods_list，如果allow_goods_list为空，才验证 deny_goods_list,
                //  如果deny_goods_list， 也为空，则表示所有商品都参与活动
                if (isset($pkg['allow_goods_list']) && $pkg['allow_goods_list']) {
                    $goods_id_list = explode(',', $pkg['allow_goods_list']);
                    $active_goods = Goods::find()->select('goods_id')
                        ->where([
                            'goods_id' => $goods_id_list,
                            'is_on_sale' => Goods::IS_ON_SALE,
                            'is_delete' => Goods::IS_NOT_DELETE,
                        ])->andWhere([
                            '>', 'goods_number', 0
                        ])->asArray()
                        ->all();
                    if ($active_goods) {
                        $event['allow_goods_list'] = array_column($active_goods, 'goods_id');
                    } else {
                        unset($event);
                        continue;
                    }
                } elseif (isset($pkg['deny_goods_list']) && $pkg['deny_goods_list']) {
                    $event['deny_goods_list'] = explode(',', $pkg['deny_goods_list']);
                } else {
                    unset($event);
                    continue;
                }
            }

            file_put_contents($cache_file_name, json_encode($active_events));
        } else {
            echo '当前没有有效的活动';
            file_put_contents($cache_file_name, '');
            return;
        }
    }

    /**
     * 定时刷新最小价格
     */
    public function actionUpdateMinPrice()
    {
        echo '获取商品的本店售价'.PHP_EOL;
        $shop_price_rs = Goods::find()
            ->select(['goods_id', 'shop_price'])
//            ->where([
//                'is_on_sale' => 1,
//                'is_delete' => 0
//            ])
            ->asArray()
            ->all();
        $shop_price_map = array_column($shop_price_rs, 'shop_price', 'goods_id');

        echo '获取梯度价格的最小价格'.PHP_EOL;
        $goods_list = array_keys($shop_price_map);
        $volume_price_sql_rs = VolumePrice::find()->select(['goods_id', 'volume_price'])
            ->where([
                'goods_id' => $goods_list
            ])->orderBy([
                'volume_number' => SORT_ASC
            ])->asArray()
            ->all();
        $volume_price_map = array_column($volume_price_sql_rs, 'volume_price', 'goods_id');

        echo '获取商品的最小价格'.PHP_EOL;
        $result= [];
        foreach ($shop_price_map as $goods_id => $shop_price) {
            if (isset($volume_price_map[$goods_id])) {
                $result[$goods_id] = min($shop_price, $volume_price_map[$goods_id]);
            } else {
                $result[$goods_id] = $shop_price;
            }
        }

        echo '执行最小价格的更新操作'.PHP_EOL;
        $i = 0;
        foreach ($result as $goods_id => $min_price) {
            ++$i;
            $sql = ' UPDATE o_goods SET min_price = '.$min_price.' WHERE goods_id = '.$goods_id;
            Yii::$app->db->createCommand($sql)->execute();
            echo 'o_goods.goods_id = '.$goods_id.' | min_price = '.$min_price.PHP_EOL;
            if ($i % 100 ==  0) {
                sleep(2);
            }
        }

    }

    /**
     * 每天执行一次商品综合排序值的更新，须放在 cron/update-min-price 脚本之后再执行
     * 同一维度的排序，【较优先】的排序值最小值 要大于 【次优先】的排序值的最大值，在较优先在排序值上+次有限的最大值避免以下犯上
     * 第一维度
     *      第一优先 商品 sort_order [0~65535]
     * 第二维度
     *      第二优先 销量 销售额度 *3 + 商品销量×50 + 下单客户数 * 200 + 订单数量×30
     *      第三优先 品牌 （sort_order [0~255] + 1） *100
     *      第四优先 浏览量 click_count
     *      第五优先 折扣 百分比 × 100
     * 当有排序值超过MySQL的INT最大数（4294967295，单SKU最大数量167771）值时，调整销量的时间段
     */
    public function actionSortGoods()
    {
        $maxArr = [
            //  销售额
            'goods_amount' => [
                'goods_id' => 0,
                'value' => 0,
            ],
            //  销售数量
            'sold_count' => [
                'goods_id' => 0,
                'value' => 0,
            ],
            //  下单用户数
            'usersCount' => [
                'goods_id' => 0,
                'value' => 0,
            ],
            //  订单数量
            'order_count' => [
                'goods_id' => 0,
                'value' => 0,
            ],
            //  品牌排序值
            'brand_sort_order' => [
                'goods_id' => 0,
                'value' => 0,
            ],
            //  商品浏览量
            'click_count' => [
                'goods_id' => 0,
                'value' => 0,
            ],
            //  折扣 * 100
            'discount' => [
                'goods_id' => 0,
                'value' => 0,
            ],
        ];
        //  获取当前所有商品对应的 除本公司员工以外的所有会员的 已支付订单
        $goods_tb = Goods::tableName();

        $GoodsQuery = Goods::find()
            ->joinWith([
                'brand',
                'orderGoods',
                'orderGoods.orderInfo',
            ])->distinct($goods_tb.'goods_id');


        $goods_list = [];
        $i = 0;

        $str = '';
        foreach ($GoodsQuery->batch() as $itemList) {
            $subStr = '';
            foreach ($itemList as $item) {
                $connect = Yii::$app->db;

                $goods_list[$item->goods_id] = [
                    'order_count' => 0,
                    'goods_amount' => 0,
                    'sold_count' => 0,
                    'discount' => 0,
                    'users' => [],
                ];
                foreach ($item['orderGoods'] as $orderGoods) {
                    $order = OrderInfo::find()->select(['user_id', 'pay_status'])
                        ->where(['order_id' => $orderGoods->order_id])
                        ->asArray()
                        ->one();
                    if ($order['pay_status'] != OrderInfo::PAY_STATUS_PAYED) {
                        continue;
                    }
                    $users = Users::find()->select('mobile_phone')
                        ->where(['user_id' => $order['user_id']])
                        ->asArray()
                        ->one();
                    if (!$users || !isset($users['mobile_phone']) || !$users['mobile_phone']) {
                        continue;   //  已删除的帐号都是测试帐号
                    }

                    //  要过滤的用户列表——内网用户
                    $ignore_mobile_list = Yii::$app->params['employee_mobile'];
                    if (isset($users['mobile_phone']) && $users['mobile_phone'] &&
                        in_array($users['mobile_phone'], $ignore_mobile_list)) {
                        continue;
                    }

                    if ($orderGoods->is_gift == OrderGoods::IS_GIFT_NO) {
                        $goods_list[$item->goods_id]['order_count'] += 1;
                        if ($orderGoods->goods_price > 0) {
                            $goods_list[$item->goods_id]['goods_amount'] += $orderGoods->goods_number * $orderGoods->goods_price;
                        }
                    }
                    $goods_list[$item->goods_id]['sold_count'] += $orderGoods->goods_number;
                    $goods_list[$item->goods_id]['users'][] = $orderGoods->orderInfo->user_id;
                }

                $usersList = array_unique($goods_list[$item->goods_id]['users']);
                $goods_list[$item->goods_id]['usersCount'] = count($usersList);

                if ( $item['market_price'] > 0) {
                    $rate = $item->min_price / $item->market_price;
                    if ($item['extension_code'] == 'integral_exchange') {
                        $goods_list[$item->goods_id]['discount'] = round((1 - $rate / 10) * 100);
                    } else {
                        $goods_list[$item->goods_id]['discount'] = round((1 - $rate) * 100);
                    }
                }

                //  统计各项最大值
                if ($goods_list[$item->goods_id]['goods_amount'] > $maxArr['goods_amount']['value']) {
                    $maxArr['goods_amount']['goods_id'] = $item->goods_id;
                    $maxArr['goods_amount']['value'] = $goods_list[$item->goods_id]['goods_amount'];
                }
                if ($goods_list[$item->goods_id]['sold_count'] > $maxArr['sold_count']['value']) {
                    $maxArr['sold_count']['goods_id'] = $item->goods_id;
                    $maxArr['sold_count']['value'] = $goods_list[$item->goods_id]['sold_count'];
                }
                if ($goods_list[$item->goods_id]['usersCount'] > $maxArr['usersCount']['value']) {
                    $maxArr['usersCount']['goods_id'] = $item->goods_id;
                    $maxArr['usersCount']['value'] = $goods_list[$item->goods_id]['usersCount'];
                }
                if ($goods_list[$item->goods_id]['order_count'] > $maxArr['order_count']['value']) {
                    $maxArr['order_count']['goods_id'] = $item->goods_id;
                    $maxArr['order_count']['value'] = $goods_list[$item->goods_id]['order_count'];
                }
                if ($item->brand['sort_order'] > $maxArr['brand_sort_order']['value']) {
                    $maxArr['brand_sort_order']['goods_id'] = $item->goods_id;
                    $maxArr['brand_sort_order']['value'] = $item->brand['sort_order'];
                }
                if ($item->click_count > $maxArr['click_count']['value']) {
                    $maxArr['click_count']['goods_id'] = $item->goods_id;
                    $maxArr['click_count']['value'] = $item->click_count;
                }
                if ($goods_list[$item->goods_id]['discount'] > $maxArr['discount']['value']) {
                    $maxArr['discount']['goods_id'] = $item->goods_id;
                    $maxArr['discount']['value'] = $goods_list[$item->goods_id]['discount'];
                }

                if (in_array($item->goods_id, [1])) {
                    $subStr .= '商品ID: '.$item->goods_id.' 第一优先:'.$item->sort_order.
                        ' 第二优先 '.round($goods_list[$item->goods_id]['goods_amount']) .' x 3'.
                        ' + '.$goods_list[$item->goods_id]['sold_count'].' × 50 '.
                        ' + '.$goods_list[$item->goods_id]['usersCount'].' × 200 '.
                        ' + '.$goods_list[$item->goods_id]['order_count'].' × 10 '.

                        ' + ('.$item->brand['sort_order'].' + 1) * 100 '.

                        ' + '.$item->click_count.

                        ' + '.$goods_list[$item->goods_id]['discount'];
                }

                $complex_order = $goods_list[$item->goods_id]['goods_amount'] * 3
                    + $goods_list[$item->goods_id]['sold_count'] * 50
                    + $goods_list[$item->goods_id]['usersCount'] * 200
                    + $goods_list[$item->goods_id]['order_count'] * 10

                    + ($item->brand['sort_order'] + 1) * 100

                    + $item->click_count

                    + $goods_list[$item->goods_id]['discount'];

                $complex_order = intval($complex_order);

                //  PHP_INT_MAX  = 2147483647
                if ($complex_order > PHP_INT_MAX) {
                    $complex_order = PHP_INT_MAX;
                    $subStr .= $item->goods_id.'单SKU订单量超出订单属相上限，调整获取交易信息的时段';
                }

                if (in_array($item->goods_id, [1])) {
                    $subStr .= '--------综合排序值:'.$complex_order.PHP_EOL;
                }

                if ($complex_order > 0) {
                    $sql = ' UPDATE '.Goods::tableName().' SET complex_order = '.$complex_order.
                        ' WHERE goods_id = '.$item->goods_id.' LIMIT 1 ';
                    $connect->createCommand($sql)->execute();
                } else {
                    $subStr .= '***** ERROR goods_id = '.$item->goods_id.' complex_order = '.$complex_order.' *****';
                }

            }
            $i++;
            echo '每次遍历100条记录，第 '.$i.' 次遍历 ：'.$subStr.PHP_EOL;
            $str .= $subStr;
        }

        if (!empty($maxArr)) {
            $str .= PHP_EOL.json_encode($maxArr);
        }

        if (!$str) {
            echo '查询商品信息失败，请重试'.PHP_EOL;
            \Yii::error(DateTimeHelper::getFormatDateTime().'查询商品信息失败，请重试');
        } else {
            echo ' total :'.PHP_EOL.$str.PHP_EOL;
        }
    }

    /**
     * 设置近两周上架的商品为新品上架
     * o_goods_tag tag_id = 1;
     */
    public function actionSetNewGoods()
    {
        $newGoodsIdList = [];
        $goodsIsNewTerm = Yii::$app->params['goods_is_new_term'];
        $splitPoint = DateTimeHelper::getFormatGMTTimesTimestamp() - $goodsIsNewTerm;
        echo '格林威治时间戳'.$splitPoint.'之后上架的商品应置为新品上架，之前的商品应去掉新品上架标签';

        $db = Yii::$app->db;

        //  删除所有的新品上架商品
        $cancelNewSql = ' DELETE FROM o_goods_tag WHERE tag_id = 1 ';
        $db->createCommand($cancelNewSql)->execute();

        $newGoodsSql = ' SELECT goods_id FROM o_goods '.
            ' WHERE is_on_sale = 1 AND is_delete = 0 AND add_time > '.$splitPoint;
        $rs = $db->createCommand($newGoodsSql)->queryAll();
        if ($rs) {
            $newGoodsIdList = array_column($rs, 'goods_id');
        }

        //  符合新品上架的商品和配置为新品上架的商品 添加到 o_goods_tag 表中
        $new_tag_goods_id_list = Yii::$app->params['new_tag_goods_id_list'];
        if ($new_tag_goods_id_list) {
            $newGoodsIdList = array_merge($newGoodsIdList, $new_tag_goods_id_list);
            $newGoodsIdList = array_unique($newGoodsIdList);
        }

        $values = '';
        if ($newGoodsIdList) {
            foreach ($newGoodsIdList as $goods_id) {
                $values .= ' ('.$goods_id.', 1),';
            }
            $values = trim($values, ',');

            $set_new_sql = ' INSERT INTO o_goods_tag (goods_id, tag_id) VALUES '.$values.';';
            $db->createCommand($set_new_sql)->execute();
        }
    }

    /**
     * 运费都更新为包邮
     */
    public function actionUpdateShippingConfig() {
        $shipping = Shipping::findOne(['shipping_code' => 'free']);
        if(!empty($shipping)) {
            Brand::updateAll(['shipping_id' => $shipping->shipping_id]);
        }
    }

    /**
     *
     * 每天定时读取前一天的日志，统计入库。
     *
     * （1）遍历日志，统计
     * （2）用户行为入库
     * （3）搜索关键词入库
     * (4)插入防重复
     *
     * 日志json_decode后的格式：
     * $data = [
     *      'u' => $_SESSION['user_id'],  // user_id用户
     *      'p' => '',  // platform 来源平台 ['m', 'pc', 'ios', 'android']
     *      'a' => '',  // action   动作类别 ['login', 'visit', 'buy', 'pay', 'search', 'send_msg']
     *      'e' => ''   // ext_info 扩展信息 [[1,2,3], 完整url, 操作数量, 操作数量, 关键词, [方法名，订单号]]
     *      't' => ''   // 北京时间的时间戳
     * ]
     */
    public function actionCountLog() {
        //  （1）遍历日志，统计
        $tag_log_base_path = Yii::$app->params['tag_log_base_path'];
        $date = date('Y-m-d', strtotime('-1 day'));
//        $date = '2017-01-08';
        $fileName = $date.'.log';
        $file = $tag_log_base_path.$fileName;
        if (!file_exists($file)) {
            echo '指定的日志文件不存在，请检查代码'.PHP_EOL;
            return 1;
        }
        $handle  = fopen($file, 'r') or die('文件不存在或不可读');

        $rs = [];
        $search = [];

        while (!feof($handle)) {
            $json = fgets($handle, 2048);
            $data = json_decode($json, true);
            if (!empty($data['u'])) {
                $userId = $data['u'];
            } else {
                break;
            }

            if (!empty($data['p'])) {
                $platForm = $data['p'];
            } else {
                break;
            }

            if (empty($rs[$userId][$platForm])) {
                $rs[$userId][$platForm] = [
                    'user_id' => $userId,
                    'login_times' => 0,
                    'wx_login_times' => 0,
                    'click_times' => 0,
                    'order_count' => 0,
                    'pay_count' => 0,
                ];
            }
            switch ($data['a']) {
                case 'login':
                    $rs[$userId][$platForm]['login_times'] += 1;
                    break;
                case 'visit':
                    $rs[$userId][$platForm]['click_times'] += 1;
                    break;
                case 'buy':
                    $rs[$userId][$platForm]['order_count'] += (int)$data['e'];
                    break;
                case 'pay':
                    $rs[$userId][$platForm]['pay_count'] += (int)$data['e'];
                    break;
                case 'search':
                    $search[$data['p']][] = $data['e'];
                    break;
                default :
                    break;

            }
        }

        fclose($handle);

        $connectionDboa = Yii::$app->dboa;
        $transaction = $connectionDboa->beginTransaction();
        try {
            //  （2）用户行为入库
            $i = 0;
            if ($rs) {
                foreach ($rs as $userId => $userLog) {

                    foreach ($userLog as $platForm => $item) {
                        $sql = ' INSERT INTO oa_mark (date, user_id, plat_form, login_times, click_times, order_count, pay_count) ' .
                            ' VALUE (' .
                            '\'' . $date . '\', ' .
                            $userId . ', ' .
                            "'" .$platForm."', " .
                            $item['login_times'] . ', ' .
                            $item['click_times'] . ', ' .
                            $item['order_count'] . ', ' .
                            $item['pay_count'] .
                            ') ';

                        $connectionDboa->createCommand($sql)->execute();
                        $i++;
                    }
                }
            }
            echo '入库 '.$i.' 条用户行为记录'.PHP_EOL;

            //  （3）搜索关键词入库
            $j = 0;
            foreach ($search as $platform => $map) {
                $keywordsMap = array_count_values($map);

                foreach ($keywordsMap as $keywords => $count) {
                    $sql = ' INSERT INTO oa_keywords (date, platform, keywords, count) VALUE ('.
                        '\''.$date.'\', '.
                        '\''.$platform.'\', '.
                        '\''.$keywords.'\', '.
                        $count.
                        ') ';
                    $connectionDboa->createCommand($sql)->execute();
                    $j++;
                }
            }
            echo '入库 '.$j.' 条关键词搜索记录'.PHP_EOL;

            $newFile = $tag_log_base_path.'bak/'.$fileName;; //新目录
            copy($file,$newFile); //拷贝到新目录
            unlink($file); //删除旧目录下的文件
            echo '文件已转移到备份目录'.PHP_EOL;

            $transaction->commit();
        } catch (Exception $e) {
            echo json_encode($e);
            $transaction->rollback();
        }

    }

    /**
     * ?貌似当前没有用到
     * 更新商品与其他筛选条件的对应关系缓存
     * 如：品牌、分类、国家、功效、活动
     */
    public function actionUpdateGoodsMapCache()
    {
        $cache = Yii::$app->cache;
        //  1、品牌 对应的 商品列表
        $brand_goods_map = Goods::getBrandGoodsMap();
        $cache->set('brand_id_goods_map', $brand_goods_map, 88000);

        //  2、二级分类 对应的 商品列表
        $cat_goods_map = Goods::getCatGoodsMap();
        $cache->set('sub_cat_id_goods_map', $cat_goods_map, 88000);

        //  3、活动/标签 对应的 商品列表
        $tag_goods_map = Goods::getTagGoodsMap();
        $cache->set('tag_goods_map', $tag_goods_map, 88000);

        //  4、产地（国家） 对应的 商品列表   产地（国家）的attr_id = 165
        $origin_goods_map = GoodsAttr::getAttrGoodsMap(165);
        $cache->set('origin_goods_map', $origin_goods_map, 88000);

        //  5、功效 对应的 商品列表   功效的attr_id = 211
        $effect_goods_map = GoodsAttr::getAttrGoodsMap(211);
        $cache->set('effect_goods_map', $effect_goods_map, 88000);
    }

    /**
     * 生成订单组
     */
    public function actionMakeOrderGroup() {
        foreach (OrderInfo::find()->each(30) as $orderInfo) {
            if (empty($orderInfo['group_id'])) {
                $orderInfo['group_id'] = $orderInfo['order_sn'];
            }
            $orderInfo->save(false);

            $group = OrderGroup::findOne([
                'group_id' => $orderInfo['group_id'],
            ]);
            if (empty($group)) {
                $orderGroup = new OrderGroup();
                $orderGroup['user_id'] = $orderInfo['user_id'];
                $orderGroup['group_id'] = $orderInfo['group_id'];
                $orderGroup['create_time'] = $orderInfo['add_time'];
                $orderGroup->save(false);
            }
        }
    }

    public function actionCancelAllUnpayOrders() {
        $errors = [];
        $modified = [];
        foreach (OrderInfo::find()->each(30) as $orderInfo) {
            if (in_array($orderInfo['order_status'], [
                OrderInfo::ORDER_STATUS_UNCONFIRMED,
                OrderInfo::ORDER_STATUS_CONFIRMED,
            ]) && $orderInfo['pay_status'] == OrderInfo::PAY_STATUS_UNPAYED
            && $orderInfo['shipping_status'] == OrderInfo::SHIPPING_STATUS_UNSHIPPED) {
                $orderInfo['order_status'] = OrderInfo::ORDER_STATUS_CANCELED;
                if (!$orderInfo->save()) {
                    $errors[] = $orderInfo['order_id'];
                }
                else {
                    $modified[] = $orderInfo['order_id'];
                }
            }
        }
        echo 'errors = '. json_encode($errors). ', modified = '. json_encode($modified);
    }

    public function actionClearSmsIp() {
        SmsIp::deleteAll();
    }

    /**
     * 每天晚上00:00:00 处理过期的活动
     *
     * 【1】修改 过期优惠券的状态 为已过期
     * 【2】修改 过期活动的状态 为已过期
     * 【3】修正 满赠、满减、优惠券 的活动标签
     *  (1)获取商品、活动、标签的关系模型
     *  (2)遍历 获取 商品应该有的标签
     *  (3)删除现有标签
     *  (4)插入新标签
     */
    public function actionCheckEvents()
    {
        $time = date('Y-m-d H:i:s', time());

        //  【1】修改 过期优惠券的状态 为已过期;如果优惠券设置销毁过期优惠券则销毁
        echo '过期优惠券的状态 为已过期'.PHP_EOL;
        CouponRecord::updateAll(
            [
                'status' => CouponRecord::COUPON_STATUS_EXPIRED
            ],
            ' status = '.CouponRecord::COUPON_STATUS_UNUSED.' AND end_time < "'.$time.'" AND user_id > 0 '
        );

        CouponRecord::updateAll(
            [
                'status' => CouponRecord::COUPON_STATUS_UNUSED
            ],
            ' status != '.CouponRecord::COUPON_STATUS_UNUSED.' AND end_time > "'.$time.'" AND used_at = 0 '
        );

        CouponRecord::updateAll(
            [
                'status' => CouponRecord::COUPON_STATUS_USED
            ],
            " status != ".CouponRecord::COUPON_STATUS_USED." AND group_id != '' AND used_at > 0  "
        );

        //  获取设置了要自动销毁的优惠券活动id
        $autoDestroyEvent = Event::find()
            ->select(['event_id'])
            ->where([
                'event_type' => Event::EVENT_TYPE_COUPON,
                'auto_destroy' => Event::AUTO_DESTROY_YES,
            ])->all();
        $autoDestroyEventIdList = array_column($autoDestroyEvent, 'event_id');
        //  销毁没有用到的 指定要自动销毁 的优惠券
        echo '销毁没有用到的 指定要自动销毁 的优惠券 event_id : '.json_encode($autoDestroyEventIdList).PHP_EOL;
        CouponRecord::deleteAll(
            [
                'event_id' => $autoDestroyEventIdList,
                'user_id' => 0
            ]
        );


        //  【2】修改 过期活动的状态 为已过期
        echo '修改 过期活动的状态 为已过期'.PHP_EOL;
        Event::updateAll(['is_active' => Event::IS_NOT_ACTIVE], ['<', 'end_time', $time]);

        //  【3】修正 满赠、满减、优惠券 的活动标签   活动的作用范围分为 全局、直发、品牌、指定商品
        //  (1)获取商品、活动、标签的关系模型
        $eventTagMap = Event::$eventTagMap;
        $checkTags = array_values($eventTagMap);    //  tag_id
        $checkEvents = array_keys($eventTagMap);    //  event_type

        $now = date('Y-m-d H:i:s', time());
        //  获取当前有效的所有活动
        $eventList = Event::find()
            ->select([Event::tableName().'.event_id', 'effective_scope_type', 'event_type', 'event_name', 'receive_type'])
            ->joinWith([
                'fullCutRule',
                'eventToBrand',
                'eventToGoods',
            ])->where([
                'event_type' => $checkEvents,
                'is_active' => Event::IS_ACTIVE,
            ])->andWhere(['<', 'start_time', $now])
            ->andWhere(['>', 'end_time', $now])
            ->all();

        GoodsTag::deleteAll(['tag_id' => $checkTags]);
        if (!empty($eventList)) {
            foreach ($eventList as $event) {
                //  如果是全局可领取优惠券，则其他活动不需要处理
                if ($event->effective_scope_type == Event::EFFECTIVE_SCOPE_TYPE_ALL){
                    if ($this->checkEventNeedTag($event)) {
                        $needTagGoodsQuery = Goods::find()
                            ->select(['goods_id'])
                            ->where([
                                'is_on_sale' => Goods::IS_ON_SALE,
                                'is_delete' => Goods::IS_NOT_DELETE,
                            ]);

                        if (isset($eventTagMap[$event->event_type])) {
                            $tagId = $eventTagMap[$event->event_type];
                            $this->setGoodsTag($needTagGoodsQuery, $tagId);

                            echo 'event_id = '.$event->event_id.'; event_type = '.$event->event_type.
                                '; event_name = '.$event->event_name.'; effective_scope_type = '.$event->effective_scope_type.
                                '; tagId = '.$tagId.PHP_EOL;
                        } else {
                            echo '$event->event_type = '.$event->event_type.PHP_EOL;
                        }
                    }
                    break;
                } elseif ($event->effective_scope_type == Event::EFFECTIVE_SCOPE_TYPE_ZHIFA) {
                    if ($this->checkEventNeedTag($event)) {
                        $needTagGoodsQuery = Goods::find()
                            ->select(['goods_id'])
                            ->where([
                                'is_on_sale' => Goods::IS_ON_SALE,
                                'is_delete' => Goods::IS_NOT_DELETE,
                                'supplier_user_id' => 1257
                            ]);
                        if (isset($eventTagMap[$event->event_type])) {
                            $tagId = $eventTagMap[$event->event_type];
                            $this->setGoodsTag($needTagGoodsQuery, $tagId);

                            echo 'event_id = '.$event->event_id.'; event_type = '.$event->event_type.
                                '; event_name = '.$event->event_name.'; effective_scope_type = '.$event->effective_scope_type.
                                '; tagId = '.$tagId.PHP_EOL;
                        } else {
                            echo '$event->event_type = '.$event->event_type.PHP_EOL;
                        }
                    }
                } elseif (
                    $event->effective_scope_type == Event::EFFECTIVE_SCOPE_TYPE_BRAND
                    && !empty($event->eventToBrand)
                ) {
                    if ($this->checkEventNeedTag($event)) {
                        $brandIdList = ArrayHelper::getColumn($event->eventToBrand, 'brand_id');

                        $needTagGoodsQuery = Goods::find()
                            ->select(['goods_id'])
                            ->where([
                                'is_on_sale' => Goods::IS_ON_SALE,
                                'is_delete' => Goods::IS_NOT_DELETE,
                                'brand_id' => $brandIdList
                            ]);

                        if (isset($eventTagMap[$event->event_type])) {
                            $tagId = $eventTagMap[$event->event_type];
                            $this->setGoodsTag($needTagGoodsQuery, $tagId);

                            echo 'event_id = '.$event->event_id.'; event_type = '.$event->event_type.
                                '; event_name = '.$event->event_name.'; effective_scope_type = '.$event->effective_scope_type.
                                '; tagId = '.$tagId.PHP_EOL;
                        } else {
                            echo '$event->event_type = '.$event->event_type.PHP_EOL;
                        }
                    }
                } elseif (
                    $event->effective_scope_type == Event::EFFECTIVE_SCOPE_TYPE_GOODS
                    && !empty($event->eventToGoods)
                ) {
                    if ($this->checkEventNeedTag($event)) {
                        $goodsIdList = ArrayHelper::getColumn($event->eventToGoods, 'goods_id');
                        $needTagGoodsQuery = Goods::find()
                            ->select(['goods_id'])
                            ->where([
                                'is_on_sale' => Goods::IS_ON_SALE,
                                'is_delete' => Goods::IS_NOT_DELETE,
                                'goods_id' => $goodsIdList
                            ]);
                        if (isset($eventTagMap[$event->event_type])) {
                            $tagId = $eventTagMap[$event->event_type];
                            $this->setGoodsTag($needTagGoodsQuery, $tagId);

                            echo 'event_id = '.$event->event_id.'; event_type = '.$event->event_type.
                                '; event_name = '.$event->event_name.'; effective_scope_type = '.$event->effective_scope_type.
                                '; tagId = '.$tagId.PHP_EOL;
                        } else {
                            echo '$event->event_type = '.$event->event_type.PHP_EOL;
                        }
                    }
                }
            }
        } else {
            echo 'no event need tag ';
        }

        $tagCountMap = GoodsTag::find()->select(['tag_id', 'count(*) as cnt'])->groupBy('tag_id')->asArray()->all();
        echo PHP_EOL.' total tag_id => count : '.json_encode($tagCountMap).PHP_EOL;


        //  ----------------------------------------------------
        //  只判断要检查的活动类型—— 优惠券活动没有对应应标   有商品可能缺失标签，不应该从goodsTag那数据，最好从eventToGoods拿数据
        /*$query = Goods::find()
            ->joinWith('eventList')
            ->joinWith('goodsTag')
            ->where(['event_type' => $checkEvents]);

        echo '获取参与 当前有效活动 的商品信息  get goodsInfoList event.is_active = 1 ：'.
            PHP_EOL.$query->createCommand()->rawSql.PHP_EOL;

        $goodsList = $query->all();

        if (!empty($goodsList)) {
            //  (2)遍历 获取 商品应该有的标签、当前有的标签、应该删除的标签
            $goodsTagMap    = [];   //  商品应该有的标签

            foreach ($goodsList as $goods) {
                //   如果商品有参与活动，则记录【商品应该有的标签】
                if (!empty($goods->eventList)) {
                    foreach ($goods->eventList as $event) {

                        //  只判断活动标签
                        if (isset($eventTagMap[$event->event_type])) {
                            if (
                                $event->is_active == Event::IS_ACTIVE
                                && $event->start_time < $time
                                && $event->end_time > $time
                            ) {
                                //  【有则保留】
                                $goodsTagMap[] = [$goods->goods_id, $eventTagMap[$event->event_type]];
                            }
                        }
                    }
                }
            }

            //  (3)删除现有标签
            GoodsTag::deleteAll(['tag_id' => $checkTags]);

            //  (4)插入新标签
            if (!empty($goodsTagMap)) {
                $query = Yii::$app->db->createCommand()->batchInsert(
                    GoodsTag::tableName(),
                    ['goods_id', 'tag_id'],
                    $goodsTagMap
                );
                echo '【无则添加】'.PHP_EOL.$query->rawSql.PHP_EOL;
                $query->execute();
            } 
        }*/

        return true;
    }

    /**
     * 每天凌晨 00:02分 自动给商品打优惠券标
     */
    public function actionSetCouponTag()
    {
        $now = date('Y-m-d H:i:s', time());
        $tagId = 8;

        //  获取当前有效的优惠券活动，并且优惠券的领取类型为手动领取
        $eventList = Event::find()
            ->select([Event::tableName().'.event_id', 'effective_scope_type'])
            ->joinWith([
                'fullCutRule',
                'eventToBrand',
                'eventToGoods',
            ])->where([
                'event_type' => Event::EVENT_TYPE_COUPON,
                'receive_type' => Event::RECEIVE_TYPE_DRAW,
                'is_active' => Event::IS_ACTIVE,
            ])
            ->andWhere(['<', 'start_time', $now])
            ->andWhere(['>', 'end_time', $now])
            ->all();
        GoodsTag::deleteAll(['tag_id' => $tagId]);
        if (!empty($eventList)) {
            foreach ($eventList as $event) {
                //  如果是全局可领取优惠券，则其他活动不需要处理
                if ($event->effective_scope_type == Event::EFFECTIVE_SCOPE_TYPE_ALL){
                    $needTagGoodsQuery = Goods::find()
                        ->select(['goods_id'])
                        ->where([
                            'is_on_sale' => Goods::IS_ON_SALE,
                            'is_delete' => Goods::IS_NOT_DELETE,
                        ]);

                    $this->setGoodsTag($needTagGoodsQuery, $tagId);
                    break;
                } elseif ($event->effective_scope_type == Event::EFFECTIVE_SCOPE_TYPE_ZHIFA) {
                    $needTagGoodsQuery = Goods::find()
                        ->select(['goods_id'])
                        ->where([
                            'is_on_sale' => Goods::IS_ON_SALE,
                            'is_delete' => Goods::IS_NOT_DELETE,
                            'supplier_user_id' => 1257
                        ]);
                    $this->setGoodsTag($needTagGoodsQuery, $tagId);
                } elseif (
                    $event->effective_scope_type == Event::EFFECTIVE_SCOPE_TYPE_BRAND
                    && !empty($event->eventToBrand)
                ) {
                    $brandIdList = ArrayHelper::getColumn($event->eventToBrand, 'brand_id');

                    $needTagGoodsQuery = Goods::find()
                        ->select(['goods_id'])
                        ->where([
                            'is_on_sale' => Goods::IS_ON_SALE,
                            'is_delete' => Goods::IS_NOT_DELETE,
                            'brand_id' => $brandIdList
                        ]);
                    $this->setGoodsTag($needTagGoodsQuery, $tagId);
                } elseif (
                    $event->effective_scope_type == Event::EFFECTIVE_SCOPE_TYPE_GOODS
                    && !empty($event->eventToGoods)
                ) {
                    $goodsIdList = ArrayHelper::getColumn($event->eventToGoods, 'goods_id');
                    $needTagGoodsQuery = Goods::find()
                        ->select(['goods_id'])
                        ->where([
                            'is_on_sale' => Goods::IS_ON_SALE,
                            'is_delete' => Goods::IS_NOT_DELETE,
                            'goods_id' => $goodsIdList
                        ]);
                    $this->setGoodsTag($needTagGoodsQuery, $tagId);
                }
            }
        } else {
            echo 'no coupon event’ RECEIVE_TYPE = DRAW ';
        }

        $hasTagGoods = GoodsTag::find()->select(['goods_id'])->where(['tag_id' => 8])->all();
        echo PHP_EOL.' total --------- count($hasTagGoods) = '.count($hasTagGoods).PHP_EOL;
    }

    /**
     * 判断活动是否需要打标
     *
     * 需要打标的活动不是优惠券活动   就可以打标
     * 优惠券活动是用户可以手动领取的 也可以打标
     * @param $event
     * @return bool
     */
    private function checkEventNeedTag($event)
    {
        if ($event->event_type == Event::EVENT_TYPE_COUPON) {
            if ($event->receive_type == Event::RECEIVE_TYPE_DRAW) {
                $rs = true;
            } else {
                $rs = false;
            }
        } else {
            $rs = true;
        }

        return $rs;
    }

    /**
     * 设置商品标签
     * @param $needTagGoodsQuery
     * @param $tagId
     */
    private function setGoodsTag($needTagGoodsQuery, $tagId)
    {
        $hasTagGoodsIdList = [];
        $goodsTag = GoodsTag::find()->select(['goods_id'])->where(['tag_id' => $tagId])->all();
        if (!empty($goodsTag)) {
            $hasTagGoodsIdList = ArrayHelper::getColumn($goodsTag, 'goods_id');
        }
        echo ' new event --------- count($hasTagGoodsIdList) = '.count($hasTagGoodsIdList).PHP_EOL;

        $goodsTagModel = new GoodsTag();
        $goodsTagModel->tag_id = $tagId;
        foreach ($needTagGoodsQuery->batch() as $goodsList) {
            $goodsIdList = ArrayHelper::getColumn($goodsList, 'goods_id');
            $needTagGoodsIdList = array_diff($goodsIdList, $hasTagGoodsIdList);
            echo ' batch --------- count($needTagGoodsIdList) = '.count($needTagGoodsIdList).PHP_EOL;
            foreach ($needTagGoodsIdList as $goodsId) {
                $model = clone $goodsTagModel;
                $model->goods_id = $goodsId;
                if (!$model->save()) {
                    echo 'goods_id = '.$goodsId.' add tag = '.$tagId.' error '.PHP_EOL;
                } else {
//                    echo 'goods_id = '.$goodsId.' add tag = '.$tagId.'success '.PHP_EOL;
                }
            }
        }
    }

    /**
     *  清除微信站首页缓存
     */
    public function actionClearWxIndexCache()
    {
        //m站的目录
        $dir = Yii::getAlias('@mRoot').'/data/cache/caches/';


        $needcheck = [true,false];
        $showPrice = [true,false];
        $user_rank = [ 0 , 1 , 2 , 3];
        $floor_index = [0, 1, 2, 3, 4 , 5 , 6 , 7 , 8  ];
        $lang = 'zh_cn';

        foreach($user_rank as $user)
        {
            foreach($needcheck  as $need)
            {
                foreach($showPrice as $price)
                {
                    $fileName = 'index_'.sprintf('%X', crc32($user.'-'.$need.'-'.$price.'-'.$lang));
                    $dirName = substr(md5($fileName), 0, 1);
                    $filePath = $dir.$dirName.'/'.$fileName.'.php';
                    if(file_exists($filePath))
                    {
                        unlink($filePath);
                    }
                    foreach($floor_index as $floor)
                    {
                        $fileName = 'index_star_floor_'.sprintf('%X', crc32($user.'-'.$floor.'-'.$need.'-'.$price.'-'.$lang));
                        $dirName = substr(md5($fileName), 0, 1);
                        $filePath = $dir.$dirName.'/'.$fileName.'.php';
                        if(file_exists($filePath))
                        {
                            unlink($filePath);
                        }
                    }
                }
            }
        }
        Yii::info('清除微信商城缓存成功.清除时间:'.date('Y-m-d H:i:s',time()),__METHOD__);
    }

    /**
     * 清除PC商城首页缓存
     */
    public function actionClearPcIndexCache()
    {
        $dir        = Yii::getAlias('@scRoot') . '/temp/caches/';
        $user_rank  = [0, 1, 2, 3];
        $floor      = [0, 1, 2, 3, 4, 5, 6, 7, 8];
        $show_price = [true, false];
        $need_check = [true, false];
        $lang       = 'zh_cn';

        foreach ($user_rank as $rank) {
            foreach ($show_price as $show) {
                foreach ($need_check as $need) {
                    $file_name = 'index_' . sprintf('%X', crc32($rank . '-' . $show . '-' . $lang . '-' . $need));
                    $dir_name  = substr(md5($file_name), 0, 1);
                    $file_path = $dir. $dir_name. '/'. $file_name. '.php';
                    if (file_exists($file_path)) {
                        Yii::warning('unlink file_path = '. $file_path, __METHOD__);
                        unlink($file_path);
                    }
                    foreach ($floor as $fl) {
                        $file_name = 'index_star_floor_' . sprintf('%X', crc32($rank . '-' . $need . '-' . $show . '-' . $fl));
                        $dir_name  = substr(md5($file_name), 0, 1);
                        $file_path = $dir. $dir_name. '/'. $file_name. '.php';
                        if (file_exists($file_path)) {
                            Yii::warning('unlink file_path = '. $file_path, __METHOD__);
                            unlink($file_path);
                        }
                    }
                }
            }
        }
        Yii::info('清除pc商城缓存成功.清除时间:'.date('Y-m-d H:i:s',time()),__METHOD__);
    }

    public function actionHandleOldDeliveryOrderDivide() {
        //对老的订单产生分成
        $query = DeliveryOrder::find()->joinWith([
            'orderInfo orderInfo',
            'servicerDivideRecord servicerDivideRecord',
        ])->where([
            '<=',
            'orderInfo.add_time',
            1489651200,
        ])->andWhere([
            '>',
            'orderInfo.add_time',
            1488787200,
        ]);

        foreach ($query->each() as $deliveryOrder) {
            $msg = 'start delivery_sn：'. $deliveryOrder->delivery_sn. ', order_sn：'. $deliveryOrder->order_sn;
            echo $msg;
            Yii::warning($msg, __METHOD__);
            $deliveryOrder->servicerDivide();

            $msg = 'end delivery_sn：'. $deliveryOrder->delivery_sn. ', order_sn：'. $deliveryOrder->order_sn;
            echo $msg;
            Yii::warning($msg, __METHOD__);
        }
    }

    public function actionClearCache() {
        $mCacheDir = Yii::getAlias('@mRoot'). '/data/cache';
        $scCacheDir = Yii::getAlias('@scRoot'). '/temp';

        FileHelper::clearDirectory($mCacheDir);
        FileHelper::clearDirectory($scCacheDir.'/caches');
        FileHelper::clearDirectory($scCacheDir.'/compiled');
        FileHelper::clearDirectory($scCacheDir.'/static_caches');

        CacheHelper::setUserRankCache();
        CacheHelper::setShopConfigParams();
        CacheHelper::setRegionCache();
        CacheHelper::setRegionAppCache();
        CacheHelper::setRegionWechatRegisterCache();
        CacheHelper::setServicerCache();
        CacheHelper::setCategoryCache();
        CacheHelper::setGoodsCategoryCache();
        CacheHelper::setBrandCatCache();
    }

    /**
     * 每天自动打包前一天的日志，删除源文件，节省空间
     */
    public function actionZipLog()
    {
        $logPath = realpath(Yii::getAlias('@traceLog'));
        $yearMonth = date('Ym');
        $date = date('Ymd');
        echo $date.' | '.$logPath.' '.PHP_EOL;

        $files = FileHelper::findFiles($logPath, ['recursive' => false]);   //  不读取子目录
        echo count($files).' '.PHP_EOL;
        if (!empty($files)) {
            if (!is_dir($logPath.'/'.$yearMonth)) {
                mkdir($logPath.'/'.$yearMonth);
            }
            foreach ($files as $file) {
                $pathInfo = pathinfo($file);
                $fileName = explode('.', $pathInfo['filename']);

                if ($pathInfo['extension'] == 'log' && !empty($fileName[5]) && ($date > $fileName[5])) {
                    $zipFileName = $fileName[5].'.'.$fileName[0].'.zip';
                    echo 'date = '.$fileName[5].' '.$file.PHP_EOL;
                    if (in_array($fileName[0], ['m', 'sc'])) {
                        if (exec('zip '.$logPath.'/'.$yearMonth.'/'.$zipFileName.' '.$file)) {
                            echo ' zip success '.PHP_EOL;

                            if (unlink($file)) {
                                echo ' delete  success'.PHP_EOL;
                            } else {
                                continue;
                            }
                        } else {
                            continue;
                        }
                    }
                } elseif ($pathInfo['extension'] == 'zip') {
                    $yearMonth = substr($fileName[0], 0, 6);
                    if (!is_dir($logPath.'/'.$yearMonth)) {
                        mkdir($logPath.'/'.$yearMonth);
                    }
                    exec('mv '.$file.' '.$logPath.'/'.$yearMonth.'/');
                }
            }
        }
    }

    public function actionAlertSms() {
        $count = SmsIp::find()->sum('count');
        if ($count > 500) {
            SMSHelper::sendSms('13510601717', '【小美诚品】今日发送验证码短信数量超过限制，请注意');
        }
    }

    public function actionReleaseLockStock() {
        $query = GoodsLockStock::find();
        foreach ($query->each() as $lockStock) {
            $lockStock->release();
        }
    }

    /**
     * 计算文章的综合排序值
     * 创建时间 + 品牌排序值（0——255） * 7 + 阅读量 * 3
     */
    public function actionArticleComplexOrder()
    {
        $brandSortRate = 7;
        $clickRate = 3;
        $articleQuery = Article::find()
            ->joinWith(['brand'])
            ->where(['cat_id' => [33, 34]]);

        foreach ($articleQuery->batch(50) as $articleList) {
            if (!empty($articleList)) {
                foreach ($articleList as $article) {
                    if (!empty($article->brand)) {
                        $brandSort = $article->brand->sort_order;
                    } else {
                        $brandSort = 0;
                    }

                    $article->complex_order = $article->add_time + $brandSort * $brandSortRate + $article->click * $clickRate;
                    if ($article->save()) {
                        echo '文章ID = '.$article->article_id.' 的综合排序值 修正为 '.$article->complex_order.PHP_EOL;
                    } else {
                        echo '文章ID = '.$article->article_id.' 的综合排序值 修正失败 errors = '.TextHelper::getErrorsMsg($article->errors).PHP_EOL;
                    }
                }
            }
        }
    }

    public function actionClearGoodsAction() {
        //清除一个月前的数据
        $dateTime = DateTimeHelper::getFormatDateTime(time() - 30 * 24 * 60 * 60);
        GoodsAction::deleteAll([
            '<',
            'time',
            $dateTime,
        ]);
    }

    /**
     * 每隔两小时运行一次
     * 更新商品销量
     */
    public function actionUpdateSaleCount()
    {
        $start = time();
        echo __METHOD__ . ' start ' . PHP_EOL;

        $orderGoods = OrderGoods::find()->alias('orderGoods')->joinWith([
            'orderInfo orderInfo' => function ($query) {
                $query->onCondition([
                    'pay_status' => OrderInfo::PAY_STATUS_PAYED,
                ]);
            },
        ])->with('goods')->orderBy(['orderGoods.goods_id' => SORT_ASC])->groupBy('orderGoods.rec_id');

        $goods = [];
        foreach ($orderGoods->each(100) as $goodsStr) {
            if ($goodsStr['goods_id'] != 0) {
                if (isset($goods[$goodsStr['goods_id']])) {
                    $goods[$goodsStr['goods_id']]['goods_number'] += $goodsStr['goods_number'];
                } else {
                    $goods[$goodsStr['goods_id']]['base_sale_count'] = $goodsStr['goods']['base_sale_count'];
                    $goods[$goodsStr['goods_id']]['goods_number'] = $goodsStr['goods_number'];
                    $goods[$goodsStr['goods_id']]['goods_id'] = (int)$goodsStr['goods_id'];
                }
            }
        }
        echo 'saving...' . PHP_EOL;
        $sql = 'UPDATE `o_goods` SET sale_count = CASE goods_id ';
        $goodsIds = [];
        foreach ($goods as $goodsStr) {
            $sql .= ' when ' . $goodsStr['goods_id'] . ' then ' . ($goodsStr['base_sale_count'] + $goodsStr['goods_number'] * 4 + rand(0, 1));
            $goodsIds[] = $goodsStr['goods_id'];
        }

        $sql .= ' END WHERE goods_id in (' . implode(',', $goodsIds) .')';
        $result = Yii::$app->db->createCommand($sql)->execute();
        $consumed = time() - $start;

        echo 'Finished !' . $result . ' rows affected. Consumed ' . $consumed . 's' . PHP_EOL;
    }

    public function actionSetData()
    {
        $firstTime = time();
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '512M');
        //先清空本来的表
        Yii::$app->db->createCommand('truncate table o_analysis_data')->execute();
        echo 'truncate success... begin read data..' . PHP_EOL;
        $start_time = "2016-01-01 00:00:01";
        $end_time = date('Y-m-d', time());
        for ($start = strtotime($start_time); $start <= strtotime($end_time); $start += 60 * 60 * 24)  //按天遍历
        {
            //对时间戳取整
            $day = strtotime(date('Y-m-d', $start));
            $GMTTime = DateTimeHelper::getFormatGMTTimesTimestamp($day);
            //查询
            $order = OrderInfo::find()->alias('order')->joinWith([
                'orderGroup',
                'ordergoods ordergoods',
                'ordergoods.goods goods',
                'ordergoods.goods.category cat',
                'ordergoods.goods.brand brand'
            ])->where(
                ['between', 'order.add_time', $GMTTime - 60 * 60 * 24, $GMTTime]
            )->andWhere(['pay_status' => OrderInfo::PAY_STATUS_PAYED])
                ->asArray()
                ->groupBy('order_id')
                ->all();
            $goods = array();
            if (empty($order)) {
                continue;
            }

            foreach ($order as $ordersStr) {
                foreach ($ordersStr['ordergoods'] as $ordergoodsStr) {
                    $goodsStr = $ordergoodsStr['goods'];
                    //记录订单信息
                    $goods[] = array(
                        'goods_id' => $goodsStr['goods_id'],    //商品id
                        'goods_name' => isset($goodsStr['goods_name']) ? $goodsStr['goods_name'] : '',  //商品名称
                        'goods_sn' => isset($goodsStr['goods_sn']) ? $goodsStr['goods_sn'] : '',    //商品条形码
                        'goods_number' => $ordergoodsStr['goods_number'],  //销售数量
                        'goods_amount' => $ordergoodsStr['pay_price'] * $ordergoodsStr['goods_number'], //销售金额
                        'brand_id' => $goodsStr['brand_id'],    //品牌id
                        'brand_name' => isset($goodsStr['brand']['brand_name']) ? $goodsStr['brand']['brand_name'] : '',//品牌名称
                        'cat_id' => $goodsStr['cat_id'], //品类id
                        'cat_name' => isset($goodsStr['category']['cat_name']) ? $goodsStr['category']['cat_name'] : '',  //品类名称
                        'user_id' => $ordersStr['user_id'], //用户id
                        'group_id' => $ordersStr['group_id'],    //总订单号
                        'consignee' => $ordersStr['consignee'],   //收货人
                        'group_status' => OrderGroup::$order_group_status[$ordersStr['orderGroup']['group_status']],    //支付状态
                        'order_amount' => $ordersStr['orderGroup']['goods_amount'],    //订单总货款
                        'create_time' =>  DateTimeHelper::getFormatCNDateTime($ordersStr['add_time']), //下单时间
                        'pay_time' => DateTimeHelper::getFormatCNDateTime($ordersStr['pay_time'])  //支付时间
                    );
                }
            }
            //订单数据去重
            $newGoods = array();
            foreach ($goods as $key) {
                $name = (string)$key['goods_id'] . (string)$key['user_id'] . (string)$key['group_id'];
                if (isset($newGoods[$name])) {
                    $newGoods[$name]['goods_number'] += $key['goods_number'];
                    $newGoods[$name]['goods_amount'] += $key['goods_amount'];
                } else {
                    $newGoods[$name] = $key;
                }
            }
            $goods = array_values($newGoods);
            $date = date('Y-m-d', strtotime(date('Y-m-d', $day) . '-1days'));
            $goodsRows = [];
            foreach ($goods as $goodsStr) {
                $goodsRows[] = array(
                    $goodsStr['goods_id'],    //商品id
                    $goodsStr['goods_name'],  //商品名称
                    $goodsStr['goods_sn'],    //商品条形码
                    $goodsStr['group_id'],    //商品总订单id
                    $goodsStr['goods_number'],    //销售数量
                    $goodsStr['goods_amount'],    //销售金额
                    $goodsStr['cat_id'],    //品类id
                    $goodsStr['cat_name'],  //品类名称
                    $goodsStr['brand_id'],    //品牌id
                    $goodsStr['brand_name'],  //品牌名称
                    $goodsStr['user_id'],  //用户id
                    $goodsStr['consignee'],  //收货人
                    $goodsStr['group_status'], //支付状态
                    $goodsStr['order_amount'],    //总订单金额
                    $goodsStr['create_time'],  //下单时间
                    $goodsStr['pay_time'],    //支付时间
                    $date //登记时间
                );
            }
            $result = Yii::$app->db->createCommand()->batchInsert('o_analysis_data',
                ['goods_id', 'goods_name', 'goods_sn', 'group_id', 'goods_number', 'goods_amount', 'cat_id', 'cat_name',
                    'brand_id', 'brand_name', 'user_id', 'consignee', 'group_status', 'order_amount', 'create_time', 'pay_time', 'date']
                , $goodsRows
            )->execute();
            echo 'order saved , ' . $result . ' rows affected ' . ', date:' . $date . PHP_EOL;
        }

        $times = time() - $firstTime;
        echo 'times:' . $times . ' s ';
        echo $date = date('Y-m-d', time() - 60 * 60 * 24) . PHP_EOL;
        $brand = Brand::find()->select('brand_id')->where(['is_show' => Brand::IS_SHOW])->asArray()->groupBy('brand_id')->all();
        $result = 0;
        $brandRows = [];
        foreach ($brand as $brandStr) {
            $brandRows[] = [
                $brandStr['brand_id'],
                $date,
            ];
        }
        if ($brandRows) {
            $result = Yii::$app->db->createCommand()->batchInsert('o_analysis_brand', ['brand_id', 'date'], $brandRows)->execute();
        }
        echo 'brand saved , ' . $result . 'rows affected ' . PHP_EOL;

        $sku = Goods::find()->select('goods_id')->where([
            'is_on_sale' => Goods::IS_ON_SALE,
            'is_delete' => Goods::IS_NOT_DELETE
        ])->asArray()->groupBy('goods_id')->all();
        $skuRows = [];
        $result = 0;
        foreach ($sku as $skuStr) {
            $skuRows[] = [
                $skuStr['goods_id'],
                $date,
            ];
        }
        if ($skuRows) {
            $result = Yii::$app->db->createCommand()->batchInsert('o_analysis_sku', ['goods_id', 'date'], $skuRows)->execute();
        }
        echo 'sku saved , ' . $result . 'rows affected ' . PHP_EOL;
        echo 'flush redis-db..' . PHP_EOL;
        $redis = new \yii\redis\Connection([
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 15,
        ]);
        $redis->executeCommand('select 15');
        $redis->executeCommand('flushdb');
        echo 'finished!';
    }
}