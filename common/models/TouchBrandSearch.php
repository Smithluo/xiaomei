<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use brand\models\TouchBrand;

/**
 * TouchBrandSearch represents the model behind the search form about `brand\models\TouchBrand`.
 */
class TouchBrandSearch extends TouchBrand
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id'], 'integer'],
            [['brand_banner', 'brand_content', 'brand_qualification'], 'safe'],
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
        $query = TouchBrand::find();

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
        ]);

        $query->andFilterWhere(['like', 'brand_banner', $this->brand_banner])
            ->andFilterWhere(['like', 'brand_content', $this->brand_content])
            ->andFilterWhere(['like', 'brand_qualification', $this->brand_qualification]);

        return $dataProvider;
    }
}
