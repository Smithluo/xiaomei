<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/1/4
 * Time: 11:46
 */

namespace backend\models;


class IndexFullCutGoods extends \common\models\IndexFullCutGoods
{

    /**
     * 获取参与满减活动的商品
     * -- 满减活动 有 全局满减、品牌满减、指定商品满减 --
     * -- 暂时放开筛选条件，所有商品可选，支持配置首页的满减商品 --
     *
     * @param string $isActive  true：获取有效活动；false：获取失效活动；其他值或不传值：获取所有满减活动
     * @return array
     */
    public static function getFullCutGoodsList($isActive = '')
    {
        $rs = [];
        $eTb = Event::tableName();
        $gTb = Goods::tableName();
        $query = EventToGoods::find()
            ->select([$gTb.'.goods_id', $gTb.'.goods_name'])
            ->joinWith('event')
            ->joinWith('goods')
            ->where([
                $eTb.'.event_type' => Event::EVENT_TYPE_FULL_CUT
            ]);
        if ($isActive == true) {
            $query->andWhere([$eTb.'.is_active' => Event::IS_ACTIVE]);
        } elseif ($isActive === false) {
            $query->andWhere([$eTb.'.is_active' => Event::IS_NOT_ACTIVE]);
        }

        $selected = IndexFullCutGoods::find()->asArray()->all();
        $selected = array_column($selected, 'goods_id');

        $goodsList = $query->all();
        if ($goodsList) {
            foreach ($goodsList as $model) {
                $goodsName = '';
                if ($model->goods['is_delete']) {
                    $goodsName .= '[已删除] ';
                }
                if (!$model->goods['is_on_sale']) {
                    $goodsName .= '[已下架] ';
                }
                if (in_array($model['goods_id'], $selected)) {
                    $goodsName .= '[专题页已配] ';
                }
                $goodsName .= ' goods_id:'.$model->goods['goods_id'].' '.$model->goods['goods_name'];

                $rs[$model->goods['goods_id']] = $goodsName;
            }
        }

        return $rs;
    }
}