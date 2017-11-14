<?php

use yii\db\Migration;
use common\models\GoodsActivity;
use common\helper\TextHelper;
use common\helper\DateTimeHelper;
/**
 * Class m170908_013650_goods_activity_add_boxNum_buyByBox
 * 团采秒杀活动 添加 是否按箱购买 发货箱规 两个字段 用于 起订数量的判断
 * 团采秒杀活动 起订数量的判断 不再关联 原商品的配置
 */
class m170908_013650_goods_activity_add_boxNum_buyByBox extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_goods_activity', 'buy_by_box', " TINYINT NOT NULL DEFAULT '0' COMMENT '是否按箱购买' ");
        $this->addColumn('o_goods_activity', 'number_per_box', " INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '发货箱规' ");

        //  历史的团采秒杀活动，把关联商品的  buy_by_box， number_per_box 写入到 活动表中
        $activityListQuery = GoodsActivity::find()
            ->joinWith('goods');

        foreach ($activityListQuery->batch(50) as $list) {
            if (!empty($list)) {
                foreach ($list as $activity) {
                    if ($activity->start_num == $activity->goods->start_num) {
                        $activity->buy_by_box = $activity->goods->buy_by_box;
                        $activity->number_per_box = $activity->goods->number_per_box;
                    } else {
                        $activity->buy_by_box = $activity->goods->buy_by_box;
                        $activity->number_per_box = $activity->start_num;
                    }

                    if (!$activity->save()) {

                        echo '$activity 保存失败， $activity->act_id = '.$activity->act_id.
                            ', start_num = '.$activity->start_num.
                            ', buy_by_box = '.$activity->buy_by_box.
                            ', number_per_box = '.$activity->number_per_box;

                        if ($activity->hasErrors()) {
                            echo TextHelper::getErrorsMsg($activity->errors);
                        }

                        echo PHP_EOL;
                    }
                }
            }
        }
    }

    public function safeDown()
    {
        $this->dropColumn('o_goods_activity', 'buy_by_box');
        $this->dropColumn('o_goods_activity', 'number_per_box');
    }
}
