<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\KnowledgeShowBrand;

/**
 * KnowledgeShowBrandSearch represents the model behind the search form about `backend\models\KnowledgeShowBrand`.
 */
class KnowledgeShowBrandSearch extends KnowledgeShowBrand
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'sort_order'], 'integer'],
            ['platform', 'string'],
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
        $query = KnowledgeShowBrand::find();

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
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'sort_order' => $this->sort_order,
            'platform' => $this->platform,
        ]);

        return $dataProvider;
    }
}
