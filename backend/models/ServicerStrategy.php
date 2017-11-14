<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/12/19
 * Time: 14:35
 */

namespace backend\models;


class ServicerStrategy extends \common\models\ServicerStrategy
{
    /**
     * 获取分成映射表  id => percent_total
     *
     * @return array
     */
    public static function getStrategyMap()
    {
        $rs = [];
        $list = self::find()->asArray()->all();

        if (!empty($list)) {
            $rs = array_column($list, 'percent_total', 'id');
        }

        return $rs;
    }
}