<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/10 0010
 * Time: 20:58
 */

namespace common\behaviors;

use Yii;
use common\helper\DateTimeHelper;
use common\models\GoodsAction;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

class RecordGoodsActionBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'record',
            ActiveRecord::EVENT_AFTER_UPDATE => 'record',
        ];
    }

    public function record($event) {
        $goodsModel = $this->owner;

        $actionModel = new GoodsAction();
        if (isset(Yii::$app->user) && !empty(Yii::$app->user->identity['user_name'])) {
            $actionModel->user_name = Yii::$app->user->identity->mobile_phone;
        }
        else {
            $actionModel->user_name = 'unknown';
        }
        $actionModel->goods_id = $goodsModel->goods_id;
        $actionModel->goods_name = $goodsModel->goods_name;
        $actionModel->shop_price = $goodsModel->shop_price;
        $actionModel->disable_discount = $goodsModel->discount_disable;

        $volumePriceList = $goodsModel->volumePrice;
        $list = [];
        if (count($volumePriceList) > 0) {
            foreach ($volumePriceList as $price) {
                $list[] = [
                    'rank' => $price->volume_number,
                    'price' => $price->volume_price,
                ];
            }
        }
        $actionModel->volume_price = VarDumper::export($list);
        $actionModel->time = DateTimeHelper::getFormatDateTimeNow();
        $actionModel->goods_number = $goodsModel->goods_number;
        $actionModel->save();
    }
}