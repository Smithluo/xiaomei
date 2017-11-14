<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/24
 * Time: 10:15
 */

namespace api\modules\v1\models;

use common\helper\DateTimeHelper;
use common\helper\ImageHelper;
use common\helper\NumberHelper;

class GoodsActivity extends \common\models\GoodsActivity
{
    /**
     * 格式化输出数据格式
     * @return array
     */
    public function fields()
    {
        return [
            'act_id' => function($model){
                return (int)$model->act_id;
            },  //  '活动ID',
            'act_name' => function($model){
                return (string)$model->act_name;
            },  //  '活动名称',
            'act_desc' => function($model){
                return (string)$model->act_desc;
            },  //  '活动描述',
            'act_type' => function($model){
                return (int)$model->act_type;
            },  //  '活动类型',
            'goods_id' => function($model){
                return (int)$model->goods_id;
            },  //  '商品ID',
            'start_num' => function($model){
                return (int)$model->start_num;
            },  //  '起售数量',
            'limit_num' => function($model){
                return (int)$model->limit_num;
            },  //  '每单限购数量',
            'match_num' => function($model){
                return (int)$model->match_num;
            },  //  '成团数量',

            'old_price' => function($model){
                return NumberHelper::price_format($model->old_price);
            },  //  '原价',
            'act_price' => function($model){
                return NumberHelper::price_format($model->act_price);
            },  //  '团采价',
            'production_date' => function($model){
                return substr($model->production_date, 0, 10);
            },  //  '商品有效期',
            'show_banner' => function($model){
                return ImageHelper::get_image_path($model->show_banner);
            },  //  '展示图',
            'qr_code' => function($model){
                return ImageHelper::get_image_path($model->qr_code);
            },  //  '二维码',
            'product_id' => function($model){
                return (int)$model->product_id;
            },  //  'Product ID',
            'goods_name' => function($model){
                return (string)$model->goods_name;
            },  //  '商品名称',
            'goods_list' => function($model){
                return ImageHelper::get_image_path($model->goods_list);
            },  //  '商品列表(图)',
            'start_time' => function($model){
                return (int)$model->start_time;
            },  //  '开始时间',
            'end_time' => function($model){
                return (int)$model->end_time;
            },  //  '结束时间',
            'is_hot' => function($model){
                return (int)$model->is_hot;
            },  //  '热门推荐',
            'is_finished' => function($model){
                return (int)$model->is_finished;
            },  //  '状态',
            'ext_info' => function($model){
                return (string)$model->ext_info;
            },  //  '扩展信息',

        ];
    }

    /**
     * 获取当前有效的团购活动映射
     */
    public static function getGroupBuyMap()
    {
        $time = DateTimeHelper::getFormatGMTTimesTimestamp();
        $rs = self::find()->select(['act_id', 'goods_id'])
            ->where(['<', 'start_time', $time])
            ->andWhere(['>', 'end_time', $time])
            ->asArray()
            ->all();

        if ($rs) {
            return array_column($rs, 'act_id', 'goods_id');
        } else {
            return [];
        }
    }

    /**
     * 取得团购活动信息
     * @param $actId     活动ID
     * @param int $buyNum  购买数量
     * @return array
     */
    public static function getGroupBuyInfo($actId, $buyNum = 0)
    {
        $actId = intval($actId);
        $group_buy = self::find()
            ->select([
                'act_id', 'act_desc', 'start_time', 'end_time', 'is_finished', 'match_num', 'ext_info', 'match_num'
            ])->where([
                'act_id' => $actId,
                'act_type' => self::ACT_TYPE_GROUP_BUY,
            ])->asArray()
            ->one();

        /* 如果为空，返回空数组 */
        if (empty($group_buy)) {
            return [];
        }
        $ext_info = [];
        if (!empty($group_buy['ext_info'])) {
            $ext_info = unserialize($group_buy['ext_info']);
            $group_buy = array_merge($group_buy, $ext_info);
        }

        //  格式化时间
        $group_buy['formated_start_time'] = DateTimeHelper::getFormatCNDateTime($group_buy['start_time']);
        $group_buy['formated_start_date'] = substr($group_buy['formated_start_time'], 0, 10);
        $group_buy['formated_end_time'] = DateTimeHelper::getFormatCNDateTime($group_buy['end_time']);
        $group_buy['formated_end_date'] = substr($group_buy['formated_end_time'], 0, 10);


        //  格式化保证金
        if ($group_buy['deposit'] > 0) {
            $group_buy['formated_deposit'] = NumberHelper::price_format($group_buy['deposit']);
        } else {
            $group_buy['formated_deposit'] = 0.00;
        }


        //  处理价格阶梯
        $price_ladder = $group_buy['price_ladder'];
        if (!is_array($price_ladder) || empty($price_ladder)) {
            $price_ladder = array(array('amount' => 0, 'price' => 0));
        } else {
            foreach ($price_ladder as $key => $amount_price) {
                $price_ladder[$key]['formated_price'] = NumberHelper::price_format($amount_price['price']);
            }
        }
        $group_buy['price_ladder'] = $price_ladder;


        //  计算活动进度
        $stat = [];
        $res = OrderGoods::find()
            ->joinWith('goodsActivity')
            ->joinWith('orderInfo')
            ->select([
                'SUM('.OrderGoods::tableName().'.goods_number) AS goods_number',
                OrderGoods::tableName().'.goods_id',

            ])->where([
                'between',
                OrderInfo::tableName().'.pay_time',
                GoodsActivity::tableName().'.start_time',
                GoodsActivity::tableName().'.end_time'
            ])->andWhere([
                GoodsActivity::tableName().'.act_type' => GoodsActivity::ACT_TYPE_GROUP_BUY
            ])->andWhere([
                GoodsActivity::tableName().'.act_id' => $actId,
            ])->groupBy(GoodsActivity::tableName().'.act_id')
            ->one();

        if  ($res) {
            $stat = [
                'valid_goods' => $res['goods_number'],
                'restrict_amount' => !empty($ext_info) ? $ext_info['restrict_amount'] : 0,
                'group_progress' => bcdiv(
                        $res['goods_number'],
                        $group_buy['match_num'],
                        4) * 100,
            ];
        } else {
            $stat = [
                'valid_goods' => 0,
                'restrict_amount' => !empty($ext_info) ? $ext_info['restrict_amount'] : 0,
                'group_progress' => 0.00,
            ];
        }


        $group_buy = array_merge($group_buy, $stat);

        //  计算当前价
        $cur_price = $price_ladder[0]['price']; // 初始化
        $cur_amount = $stat['valid_goods'] + $buyNum; // 当前数量
        foreach ($price_ladder as $amount_price) {
            if ($cur_amount >= $amount_price['amount']) {
                $cur_price = $amount_price['price'];
            } else {
                break;
            }
        }
        $group_buy['cur_price'] = $cur_price;
        $group_buy['formated_cur_price'] = NumberHelper::price_format($cur_price);

        //  最终价
        $group_buy['trans_price'] = $group_buy['cur_price'];
        $group_buy['formated_trans_price'] = $group_buy['formated_cur_price'];
        $group_buy['trans_amount'] = $group_buy['valid_goods'];

        //  状态
        $group_buy['status'] = self::group_buy_status($group_buy);
        $groupStatusMap = self::$is_finished_map;
        $group_buy['status_desc'] = $groupStatusMap[$group_buy['status']];

        $group_buy['start_time'] = $group_buy['formated_start_time'];
        $group_buy['end_time'] = $group_buy['formated_end_time'];
//        $group_buy['act_banner'] = $group_buy['act_banner'];
//        $group_buy['click_num'] = $group_buy['click_num'];

        //  团拼销量的计算可能需要检查
//        $group_buy['sales_count'] = $group_buy['sales_count'] ? $group_buy['sales_count'] : 0;
        $group_buy['sales_count'] = $group_buy['valid_goods'] ? $group_buy['valid_goods'] : 0;

        $group_buy['act_desc'] = $group_buy['act_desc'];

        return $group_buy;
    }

    /**
     *
     * 取得某团购活动统计信息
     *
     * @param int $actId    团购活动id
     * @param int $deposit  保证金
     *
     * @return mixed
     *  [
     *      total_order     总订单数
     *      total_goods     总商品数
     *      valid_order     有效订单数
     *      valid_goods     有效商品数
     * ]
     */
    public static function groupBuyStat($actId, $deposit = 0) {
        $actId = intval($actId);

        $act = self::find()
            ->where([
                'act_id' => $actId,
                'act_type' => self::ACT_TYPE_GROUP_BUY,
            ])->one();

        if (!$act) {
            return [];
        }
        //  取得团购活动商品ID
        $goodsId = $act->goods_id;

        //  取得总订单数和总商品数
        $orderGoods = OrderGoods::find()
            ->joinWith('goodsActivity')
            ->joinWith('orderInfo')
            ->where([
                OrderGoods::tableName().'.goods_id' => $goodsId,
                OrderInfo::tableName().'.extension_code' => 'group_buy',
                OrderInfo::tableName().'.extension_id' => $actId,
               /* OrderInfo::tableName().'.order_status' => [
                    OrderInfo::ORDER_STATUS_CONFIRMED, OrderInfo::ORDER_STATUS_UNCONFIRMED
                ],*/
            ])->andWhere([
                'between', OrderInfo::tableName().'.pay_time', $act->start_time, $act->end_time
            ])->asArray()
            ->all();

        $total_order = count($orderGoods);
        if ($total_order == 0) {
            $total_goods = 0;
        } else {
            $total_goods = array_sum(array_column($orderGoods, 'goods_number'));
        }

        /**
         *  如果有保证金 取得有效订单数和有效商品数
        $deposit = floatval($deposit);
        if ($deposit > 0 && $total_order > 0) {
            $sql .= " AND (o.money_paid + o.surplus) >= '$deposit'";
            $row = M()->getRow($sql);
            $stat['valid_order'] = $row['total_order'];
            if ($stat['valid_order'] == 0) {
                $stat['valid_goods'] = 0;
            } else {
                $stat['valid_goods'] = $row['total_goods'];
            }
        } else {
            $stat['valid_order'] = $total_order;
            $stat['valid_goods'] = $total_goods;
        }
        */

        return [
            'valid_order' => $total_order,
            'valid_goods' => $total_goods,
        ];
    }

    /**
     * 获得团购的状态
     *
     * @param $group_buy    array   团购信息
     * @return int  状态值
     */
    public static function group_buy_status($group_buy) {
        $now = DateTimeHelper::getFormatGMTTimesTimestamp();
        if ($group_buy['is_finished'] == 0) {
            //  未处理
            if ($now < $group_buy['start_time']) {
                $status = self::STATUS_PRE_START;
            } elseif ($now > $group_buy['end_time']) {
                $status = self::STATUS_FINISHED;
            } else {
                if ($group_buy['valid_goods'] >= $group_buy['match_num']) {
                    $status = self::STATUS_SETTLED;
                }
                elseif ($group_buy['restrict_amount'] == 0 || $group_buy['valid_goods'] < $group_buy['restrict_amount']) {
                    $status = self::STATUS_UNDER_WAY;
                } else {
                    $status = self::STATUS_FINISHED;
                }
            }
        } elseif ($group_buy['is_finished'] == self::STATUS_SETTLED) {
            //  已处理，团购成功
            $status = self::STATUS_SETTLED;
        } elseif ($group_buy['is_finished'] == GBS_FAIL) {
            //  已处理，团购失败
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
    
}