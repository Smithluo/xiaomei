<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/10 0010
 * Time: 20:58
 */

namespace common\behaviors;

use common\helper\SwiftMailerHelper;
use common\models\Goods;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class ChangeOnSaleStockBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'listen',
        ];
    }

    /**
     * @param $event
     * 监听 库存和上下架状态
     * 测试通过
     */
    public function listen($event) {
        ini_set('max_execution_time', 180);
        ini_set('memory_limit', '512M');
        $changedAttributes = $event->changedAttributes;
        $goodsInfo = $this->owner;

        //  发邮件给内部人员
        $content = '';

        if (isset($changedAttributes['is_on_sale'])) {
            if ($goodsInfo->is_on_sale == Goods::IS_ON_SALE) {
                $content .= '【商品上架】 ';
            } elseif ($goodsInfo->is_on_sale == Goods::NOT_ON_SALE) {
                $content .= '【商品下架】 ';
            }
        }

        if (isset($changedAttributes['goods_number'])) {
            if ($goodsInfo->goods_number < $goodsInfo->start_num) {
                $content .= '【库存不足】';
            } elseif (
                $goodsInfo->goods_number >= $goodsInfo->start_num &&
                $changedAttributes['goods_number'] < $goodsInfo->start_num
            ) {
                $content .= '【到货】';
            }
        }

        if (!empty($content)) {
            $setTo = Yii::$app->params['mailGroup']['goodsOperater'];
            $subject = '商品通知：'.$content;
            $mailContent = '商品ID：'.$goodsInfo->goods_id.
                ', 商品名称：'.$goodsInfo->goods_name.
                '，当前库存：'.$goodsInfo->goods_number.$content;

            Yii::warning('发送邮件通知'.$subject.'; 收件人：'.json_encode($setTo).'; 邮件内容：'.$mailContent, __METHOD__);
            SwiftMailerHelper::sendMail($setTo, $subject, $mailContent);
        }

    }
}