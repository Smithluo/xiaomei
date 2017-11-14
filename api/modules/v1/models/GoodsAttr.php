<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/11/16
 * Time: 7:43 PM
 */

namespace api\modules\v1\models;


class GoodsAttr extends \common\models\GoodsAttr
{

    /**
     * 获取产地（国家） 对应的 商品列表
     */
    public static function getAttrGoodsMap($attr_id, $origin = '')
    {
        $attr_goods_map = [];
        $query = self::find()->select([self::tableName().'.goods_id', self::tableName().'.attr_value'])
            ->joinWith('goods')
            ->where([
                'attr_id' => $attr_id,
                Goods::tableName().'.is_on_sale' => Goods::IS_ON_SALE,
                Goods::tableName().'.is_delete' => Goods::IS_NOT_DELETE,
            ]);

        if ($origin != '') {
            $query->andWhere(['attr_value' => $origin]);
        }
        
        $result = $query->all();
        if ($result && is_array($result)) {
            foreach ($result as $item) {
                $attr_goods_map[$item->attr_value][] = $item->goods_id;
            }
        }

        return $attr_goods_map;
    }


    /**
     * 获取功效 对应的 商品列表
     */
    public static function getEffectGoodsMap()
    {

    }
}