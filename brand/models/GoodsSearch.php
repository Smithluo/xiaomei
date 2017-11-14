<?php

namespace brand\models;

use common\models\BrandUser;
use Yii;
use common\models\Goods;
use common\models\GoodsSearch as BaseGoodsSearch;
use yii\data\ActiveDataProvider;

/**
 * CashRecordSearch represents the model behind the search form about `brand\models\CashRecord`.
 */
class GoodsSearch extends BaseGoodsSearch
{

    public function search($params)
    {
        $query = Goods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['gridPageSize'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'goods_id' => $this->goods_id,
            'cat_id' => $this->cat_id,
            'click_count' => $this->click_count,
            'brand_id' => $this->brand_id,
            'goods_number' => $this->goods_number,
            'number_per_box' => $this->number_per_box,
            'goods_weight' => $this->goods_weight,
            'market_price' => $this->market_price,
            'shop_price' => $this->shop_price,
            'min_price' => $this->min_price,
            'promote_price' => $this->promote_price,
            'promote_start_date' => $this->promote_start_date,
            'promote_end_date' => $this->promote_end_date,
            'warn_number' => $this->warn_number,
            'is_real' => $this->is_real,
            'is_on_sale' => $this->is_on_sale,
            'is_alone_sale' => $this->is_alone_sale,
            'is_shipping' => $this->is_shipping,
            'integral' => $this->integral,
            'add_time' => $this->add_time,
            'sort_order' => $this->sort_order,
            'is_delete' => $this->is_delete,
            'is_best' => $this->is_best,
            'is_new' => $this->is_new,
            'is_hot' => $this->is_hot,
            'is_spec' => $this->is_spec,
            'is_promote' => $this->is_promote,
            'bonus_type_id' => $this->bonus_type_id,
            'last_update' => $this->last_update,
            'goods_type' => $this->goods_type,
            'give_integral' => $this->give_integral,
            'rank_integral' => $this->rank_integral,
            'suppliers_id' => $this->suppliers_id,
            'is_check' => $this->is_check,
        ]);

        $goodsBrandIds = BrandUser::getGoodsBrandList(Yii::$app->user->identity['user_id']);
        if (isset($params['brand_id']) && $params['brand_id']) {
            $query->andFilterWhere(['and', ['brand_id' => $params['brand_id']], ['or', ['supplier_user_id' => 0], ['supplier_user_id' => Yii::$app->user->identity['user_id']]]]);
            if (in_array($params['brand_id'], $goodsBrandIds)) {
                $query->andFilterWhere([
                    'supplier_user_id' => Yii::$app->user->identity['user_id'],
                ]);
            }
        } elseif ($params['brand_list']) {
            $searchBrand = array_diff($params['brand_list'], $goodsBrandIds);
            $query->andFilterWhere(['and', ['brand_id' => $searchBrand], ['or', ['supplier_user_id' => 0], ['supplier_user_id' => Yii::$app->user->identity['user_id']]]]);
            $query->orFilterWhere(['supplier_user_id' => Yii::$app->user->identity['user_id']]);
        }

        $query->andFilterWhere(['like', 'goods_sn', $this->goods_sn])
            ->andFilterWhere(['like', 'goods_name', $this->goods_name])
            ->andFilterWhere(['like', 'goods_name_style', $this->goods_name_style])
            ->andFilterWhere(['like', 'provider_name', $this->provider_name])
            ->andFilterWhere(['like', 'measure_unit', $this->measure_unit])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'goods_brief', $this->goods_brief])
            ->andFilterWhere(['like', 'goods_desc', $this->goods_desc])
            ->andFilterWhere(['like', 'goods_thumb', $this->goods_thumb])
            ->andFilterWhere(['like', 'goods_img', $this->goods_img])
            ->andFilterWhere(['like', 'original_img', $this->original_img])
            ->andFilterWhere(['like', 'extension_code', $this->extension_code])
            ->andFilterWhere(['like', 'seller_note', $this->seller_note])
            ->andFilterWhere(['like', 'children', $this->children])
            ->andFilterWhere(['like', 'shelf_life', $this->shelf_life]);

        return $dataProvider;
    }
}
