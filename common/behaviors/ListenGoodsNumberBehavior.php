<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/10 0010
 * Time: 20:58
 */

namespace common\behaviors;

use common\helper\SMSHelper;
use common\models\ArrivalReminder;
use common\models\Goods;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;


class ListenGoodsNumberBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'listen',
        ];
    }

    /**
     * @param $event
     * 监听 库存和商家状态
     * 测试通过
     */
    public function listen($event) {

        ini_set('max_execution_time', 180);
        ini_set('memory_limit', '512M');
        $changedAttributes = $event->changedAttributes;
        $goodsInfo = $this->owner;

        //用于判断是不是到货提醒的商品 需要发送短信
        $status = false;
        foreach($changedAttributes as $key => $value) {
            //如果改变的字段有goods_number 或者 is_on_sale
            if(in_array($key, ['goods_number', 'is_on_sale'])) {
                //如果修改了库存 并且 是上架状态
                if($key == 'goods_number' && $goodsInfo->is_on_sale == Goods::IS_ON_SALE) {

                    if($goodsInfo->goods_number >= $goodsInfo->start_num && $value < $goodsInfo->start_num ) {
                        $status = true;
                    }

                }
                //如果修改了上架状态并且 库存大于起订量
                if($key == 'is_on_sale' && $goodsInfo->goods_number >= $goodsInfo->start_num ) {

                    if($value == Goods::NOT_ON_SALE && $goodsInfo->is_on_sale == Goods::IS_ON_SALE) {
                        $status = true;
                    }
                }
            }
        }

        if($status ) {

            $arrival = ArrivalReminder::find()
                ->with([
                    'user'
                ])
                ->where([
                    'goods_id' => $goodsInfo->goods_id,
                    'status' => ArrivalReminder::NOT_ARRIVAL,
                ])
                ->all();
            if(empty($arrival)) {
                Yii::info($goodsInfo->goods_id.'该商品没有被提醒', __METHOD__);
                return ;
            }
            $content = '【小美诚品】到货了！亲，您关注的商品：'.$goodsInfo->goods_name.' 已到货，登录小美诚品即可下单采购了';
            //发送短信
           foreach($arrival as $value) {
               $mobile = $value->user['mobile_phone'];
               if(!SMSHelper::sendSms($mobile, $content)) {
                   Yii::info('向用户'.$mobile.'发送短信成功，goods_id='. $goodsInfo['goods_id'], __METHOD__);
                   $value->status = ArrivalReminder::HAS_ARRIVAL;
                   $value->save();
               }
           }
        }
    }
}