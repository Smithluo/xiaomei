<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\IndexStarUrl;

/**
 * IndexStarUrlSearch represents the model behind the search form about `common\models\IndexStarUrl`.
 */
class IndexStarUrlSearch extends IndexStarUrl
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tab_id', 'sort_order'], 'integer'],
            [['title', 'url'], 'safe'],
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
        $query = IndexStarUrl::find();

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
            IndexStarUrl::tableName().'.id' => $this->id,
            IndexStarUrl::tableName().'.tab_id' => $this->tab_id,
            IndexStarUrl::tableName().'.sort_order' => $this->sort_order,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
