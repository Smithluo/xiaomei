<?php
/**
 * Created by PhpStorm.
 * User: Clark
 * Date: 2016-10-17
 * Time: 14:34
 */

namespace api\modules\v1\controllers;

use \Yii;
use common\models\Category;
use common\models\Goods;

class CategoryController extends BaseActiveController
{
    public $modelClass = 'common\models\Category';

    public function actionUsable()
    {
        $g_tb = Goods::tableName();
        $c_tb = Category::tableName();
        $query = Goods::find()->select([
                $g_tb.'.cat_id',
                $c_tb.'.cat_name',
                $c_tb.'.parent_id',
                'count('.$g_tb.'.goods_id) AS cnt']
            )->joinWith('category')
            ->where([
                $g_tb.'.is_on_sale' => Goods::IS_ON_SALE,
                $g_tb.'.is_delete' => Goods::IS_NOT_DELETE,
            ])->groupBy($g_tb.'.cat_id');
        $rs = $query->asArray()->all();

        $sub_cat_map = [];

        if (!empty($rs)) {
            foreach ($rs as $item) {
                $sub_cat_map[$item['parent_id']][] = [
                    'cat_id' => $item['cat_id'],
                    'cat_name' => $item['cat_name'],
                ];
            }

            $parent_cat_list = array_keys($sub_cat_map);
            $query = Category::find()->select(['cat_name', 'cat_id'])
                ->where(['cat_id' => $parent_cat_list])
                ->asArray();
            $rs = $query->all();
            $cat_map = array_column($rs, 'cat_name', 'cat_id');

            if ($sub_cat_map && $cat_map) {

                return [
                    'cat_map' => $cat_map,
                    'sub_cat_map' => $sub_cat_map,
                ];
            } else {
                return [
                    'cat_map' => $cat_map,
                    'sub_cat_map' => $sub_cat_map,
                ];
            }
        } else {
            return [];
        }
    }

}