<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\IndexPaihangGoods;

/**
 * IndexPaihangGoodsSearch represents the model behind the search form about `common\models\IndexPaihangGoods`.
 */
class IndexPaihangGoodsSearch extends IndexPaihangGoods
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'sort_order'], 'integer'],
            [['title', 'description', 'floor_id'], 'safe'],
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
        $query = IndexPaihangGoods::find()->with([
            'floor',
            'goods',
        ]);

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
            'goods_id' => $this->goods_id,
            'sort_order' => $this->sort_order,
            'floor_id' => $this->floor_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
