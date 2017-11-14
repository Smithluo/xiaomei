<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\IndexHotGoods;

/**
 * IndexHotGoodsSearch represents the model behind the search form about `common\models\IndexHotGoods`.
 */
class IndexHotGoodsSearch extends IndexHotGoods
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'sort_order'], 'integer'],
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
        $query = IndexHotGoods::find();
        $query->joinWith('goods');
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
            IndexHotGoods::tableName().'.id' => $this->id,
            IndexHotGoods::tableName().'.goods_id' => $this->goods_id,
            IndexHotGoods::tableName().'.sort_order' => $this->sort_order,
        ]);

        return $dataProvider;
    }
}
