<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GoodsCollectionItem;

/**
 * GoodsCollectionItemSearch represents the model behind the search form about `common\models\GoodsCollectionItem`.
 */
class GoodsCollectionItemSearch extends GoodsCollectionItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'coll_id', 'goods_id', 'sort_order'], 'integer'],
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
        $query = GoodsCollectionItem::find();

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
            'coll_id' => $this->coll_id,
            'goods_id' => $this->goods_id,
            'sort_order' => $this->sort_order,
        ]);

        return $dataProvider;
    }
}
