<?php
/**
 * Created by PhpStorm.
 * User: HongXunPan
 * Date: 2017/11/6
 * Time: 11:39
 */

namespace api\modules\v1\controllers;


use api\modules\v1\models\Event;
use api\modules\v1\models\GoodsActivity;
use common\helper\DateTimeHelper;
use common\helper\GoodsActivityHelper;
use common\helper\GoodsHelper;
use common\helper\ImageHelper;
use common\helper\NumberHelper;
use common\models\Goods;
use common\models\SuperPkg;

/**
 * 活动中心控制器
 * Class ActivityController
 * @package api\modules\v1\controllers
 */
class ActivityController extends BaseAuthActiveController
{
    public $modelClass = 'api\modules\v1\GoodsActivity';

    /**
     * 团采
     * @return array
     */
    public function actionGroup_buy()
    {
        $gmtTime = DateTimeHelper::gmtime();
        $groupBuyGoods = GoodsActivity::find()
            ->joinWith([
                'goods goods',
                'goods.tags'
            ])
            ->where([
                '>', 'end_time', $gmtTime,
            ])
            ->andWhere([
                'act_type' => GoodsActivity::ACT_TYPE_GROUP_BUY
            ])
            ->orderBy([
                new \yii\db\Expression('FIELD (goods.goods_number, 0)'),     //库存为0的排到后面
                'sort_order' => SORT_DESC,
                'act_id' => SORT_DESC,
            ])
            ->all();

        $teamBuyFirst = [];
        $teamBuySecond = [];
        $i = 0;
        foreach ($groupBuyGoods as $key => $groupBuy) {
            $goods = $groupBuy['goods'];
            $price = NumberHelper::price_format($groupBuy->act_price);
            $box_num = empty($groupBuy->buy_by_box) || empty($groupBuy->number_per_box) ? null : $groupBuy->number_per_box;
            $statusDesc = '进行中';
            if ($gmtTime < $groupBuy->start_time) {
                $statusDesc = '即将开始';
            } elseif ($groupBuy->start_num > $goods['goods_number']) {
                $statusDesc = '卖光了';
            }

            $teamBuyItem = [
                'act_id' => $groupBuy->act_id,
                'goods_name' => $groupBuy->goods_name,
                'end_time' => DateTimeHelper::getFormatCNDateTime($groupBuy->end_time),
                'start_time' => DateTimeHelper::getFormatCNDateTime($groupBuy->start_time),
                'num_group' => $groupBuy->match_num,    //成团数
                'start_num' => $groupBuy->start_num,    //起批量
                'note' => $groupBuy->note,  //附加说明
                'goods_thumb' => ImageHelper::getNewGoodsActivityImg($groupBuy->show_banner),
                'old_price' => NumberHelper::price_format($groupBuy->old_price),
                'min_price' => $price,
                'percent' => \common\models\GoodsActivity::insertActivityProgress(['id' => $groupBuy->act_id]) . '%',

                'goods_id' => $goods['goods_id'],
                'market_price' => $goods['market_price'],
                'measure_unit' => $goods['measure_unit'],
                'goods_number' => $goods['goods_number'],
                'number_per_box' => $box_num,
                'limit_num' => $groupBuy['limit_num'],
                'status_desc' => $statusDesc,
            ];

            if ($i++ < 4) {
                $teamBuyFirst[] = $teamBuyItem;
            } else {
                $teamBuySecond[] = $teamBuyItem;
            }

        }

        return [
            'groupBuy_first' => $teamBuyFirst,
            'groupBuy_second' => $teamBuySecond,
        ];
    }

    /**
     * 秒杀
     * @param int $saleNum
     * @return array
     */
    public function actionFlash_sale($saleNum = 10)
    {
        $flashSale = GoodsActivityHelper::getFlashSaleGoodsList($saleNum, 'api');

        $flashSaleList = [];
        foreach ($flashSale as $sale) {

            $box_num = empty($sale['goods']['buy_by_box']) ? null : $sale['goods']['number_per_box'];
            $goods = $sale['goods'];
            $countDown = '';
            $status = '';
            if ($sale['status'] == 'onGoing') {
                $status = '进行中';
                $countDown = $sale['end_time'] - DateTimeHelper::gmtime();
            } elseif ($sale['status'] == 'startEve') {

                $monthAndDay1 = DateTimeHelper::getFormatCNDateTime(time(), 'm-d');
                $monthAndDay2 = DateTimeHelper::getFormatCNDateTime($sale['start_time'], 'm-d');

                $countDown = $sale['start_time'] - DateTimeHelper::gmtime();
                if ($monthAndDay1 == $monthAndDay2) {
                    $status = DateTimeHelper::getFormatCNDateTime($sale['start_time'], 'H:i') . '即将开始';
                } else {
                    $status = $monthAndDay2 . '即将开始';
                }
            } elseif ($sale['status'] == 'sellOut') {
                $status = '抢光了';
            }

            $goodsActivity = GoodsActivity::findOne($sale['act_id']);
            $hasBuy = $goodsActivity->getActivitySaleCount('flash_sale', \Yii::$app->user->getId());
            $sale['limit_num'] -= $hasBuy;
            $maxNum = min($sale['limit_num'], $goods['goods_number']);
            if ($maxNum < 0) {
                $maxNum = 0;
            }

            $flashSaleList[] = [
                'act_id' => $sale['act_id'],
                'start_num' => $sale['start_num'],
                'act_name' => $sale['act_name'],
                'act_desc' => '每个账号限购' . $sale['limit_num'] . $goods['measure_unit'],
                'measure_unit' => $goods['measure_unit'],
                'number_per_box' => $box_num,
                'goods_number' => $maxNum,     //库存量或者最大限购量
                'goods_id' => $sale['goods_id'],
                'goods_name' => $goods['goods_name'],
                'start_time' => DateTimeHelper::getFormatCNDateTime($sale['start_time']),
                'end_time' => DateTimeHelper::getFormatCNDateTime($sale['end_time']),
                'status' => $status,
                'goods_thumb' => ImageHelper::get_image_path($goods['goods_thumb']),
                'min_price' => $sale['act_price'],
                'old_price' => $sale['old_price'],
                'count_down' => $countDown,
            ];
        }

        return $flashSaleList;
    }

    /**
     * 满赠
     * @return array
     */
    public function actionFull_gift()
    {
        $giftGoodsList = GoodsHelper::fullGiftGoodsList();
        $giftList = [];
        foreach ($giftGoodsList as $goods) {
            $giftList[] = [
                'goods_id' => $goods['goods_id'],
                'goods_name' => $goods['goods_name'],
                'event_desc' => $goods['event_desc'],
                'goods_thumb' => $goods['goods_thumb'],
                'min_price' => $goods['goods_price'],
                'market_price' => $goods['market_price'],
                'goods_time' => $goods['goods_time'],

                'start_num' => $goods['start_num'],
                'goods_number' => $goods['goods_number'],
                'buy_by_box' => Goods::$buy_by_box_map[$goods['buy_by_box']],
                'number_per_box' => $goods['number_per_box'],
                'measure_unit' => $goods['measure_unit'],
            ];
        }
        return $giftList;
    }

    /**
     * 礼包、套餐接口
     * @return array
     */
    public function actionLi_bao()
    {
        $pkgList = SuperPkg::giftPkgList();
        $pkgs = [];
        foreach ($pkgList as $pkgInfo) {
            $pkgs[] = [
                'id' => $pkgInfo['id'],
                'img' => $pkgInfo['img'],
                'pkg_name' => $pkgInfo['pkg_name'],
                'status' => $pkgInfo['status'],
                'pag_desc' => $pkgInfo['pag_desc'],
                'price' => $pkgInfo['price'],
            ];
        }

        return $pkgs;
    }

    /**
     * 满减接口
     * @return array
     */
    public function actionFull_cut()
    {
        $fullCutRs = Event::fullCutGoodsList();
        $goodsList = [];
        foreach ($fullCutRs['cutGoodsList'] as $goods) {
            $goodsList[] = [
                'goods_id' => $goods['goods_id'],
                'goods_name' => $goods['goods_name'],
                'start_num' => $goods['start_num'],
                'goods_number' => $goods['max_num'],
                'measure_unit' => $goods['measure_unit'],
                'market_price' => $goods['market_price'],
                'min_price' => $goods['price'],
                'buy_by_box' => Goods::$buy_by_box_map[$goods['buy_by_box']],
                'number_per_box' => $goods['box_num'],
                'goods_thumb' => $goods['goods_thumb'],
                'goods_time' => $goods['goods_time'],
            ];
        }
        return $goodsList;
    }
}