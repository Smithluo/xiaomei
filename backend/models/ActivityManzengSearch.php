<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ActivityManzeng;

/**
 * ActivityManzengSearch represents the model behind the search form about `common\models\ActivityManzeng`.
 */
class ActivityManzengSearch extends ActivityManzeng
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'sort_order'], 'integer'],
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
        $query = ActivityManzeng::find();

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
            'goods_id' => $this->goods_id,
            'sort_order' => $this->sort_order,
        ]);

        return $dataProvider;
    }
}
