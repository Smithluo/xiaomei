<?php

namespace service\models;

use common\models\ServicerStrategy;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use service\models\Brand;

/**
 * BrandSearch represents the model behind the search form about `service\models\Brand`.
 */
class BrandSearch extends Brand
{
    public $percent_total;
    public $percent_level_2;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'sort_order', 'is_show', 'album_id', 'servicer_strategy_id'], 'integer'],
            [['brand_name', 'brand_depot_area', 'brand_logo', 'brand_logo_two', 'brand_bgcolor', 'brand_policy', 'brand_desc', 'brand_desc_long', 'short_brand_desc', 'site_url', 'brand_tag', 'percent_total'], 'safe'],
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

        $query->joinWith('servicerStrategy');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['gridPageSize'],
            ],
        ]);

//        $dataProvider->setSort([
//            'attributes' => [
//                'percent_total' => [
//                    'asc' => [ServicerStrategy::tableName().'.percent_total' => SORT_ASC],
//                    'desc' => [ServicerStrategy::tableName().'.percent_total' => SORT_DESC],
//                    'label' => '订单编号',
//                ],
//                'brand_id' => [
//                    'asc' => ['brand_id' => SORT_ASC],
//                    'desc' => ['brand_id' => SORT_DESC],
//                    'label' => '品牌ID',
//                ],
//            ]
//        ]);

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
            'is_show' => 1,
            'album_id' => $this->album_id,
            'servicer_strategy_id' => $this->servicer_strategy_id,
        ]);

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
            ->andFilterWhere(['like', 'brand_tag', $this->brand_tag])
            ->andFilterWhere(['like', ServicerStrategy::tableName().'.percent_total', $this->percent_total])
        ;

        return $dataProvider;
    }
}
