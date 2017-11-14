<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Brand;

/**
 * BrandQuery represents the model behind the search form about `common\models\Brand`.
 */
class BrandQuery extends Brand
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'sort_order', 'is_show', 'album_id'], 'integer'],
            [['brand_name', 'brand_depot_area', 'brand_logo', 'brand_logo_two', 'brand_bgcolor', 'brand_policy', 'brand_desc', 'brand_desc_long', 'short_brand_desc', 'site_url', 'brand_tag'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Brand::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'brand_id' => $this->brand_id,
            'sort_order' => $this->sort_order,
            'is_show' => $this->is_show,
            'album_id' => $this->album_id,
        ]);
        if (isset($params['brand_list']) && $params['brand_list']) {
            $query->andFilterWhere(['brand_id' => $params['brand_list']]);
        }

        $query->andFilterWhere(['like', 'brand_name', $this->brand_name])
            ->andFilterWhere(['like', 'brand_depot_area', $this->brand_depot_area])
            ->andFilterWhere(['like', 'brand_logo', $this->brand_logo])
            ->andFilterWhere(['like', 'brand_logo_two', $this->brand_logo_two])
            ->andFilterWhere(['like', 'brand_bgcolor', $this->brand_bgcolor])
            ->andFilterWhere(['like', 'brand_policy', $this->brand_policy])
            ->andFilterWhere(['like', 'brand_desc', $this->brand_desc])
            ->andFilterWhere(['like', 'brand_desc_long', $this->brand_desc_long])
            ->andFilterWhere(['like', 'short_brand_desc', $this->short_brand_desc])
            ->andFilterWhere(['like', 'site_url', $this->site_url])
            ->andFilterWhere(['like', 'brand_tag', $this->brand_tag]);

        return $dataProvider;
    }
}
