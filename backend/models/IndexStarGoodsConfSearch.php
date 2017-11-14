<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\IndexStarGoodsConf;

/**
 * IndexStarGoodsConfSearch represents the model behind the search form about `common\models\IndexStarGoodsConf`.
 */
class IndexStarGoodsConfSearch extends IndexStarGoodsConf
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'tab_id', 'sort_order'], 'integer'],
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
        $query = IndexStarGoodsConf::find();
        $query->joinWith('goods goods');
        $query->joinWith('tab tab');

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
            IndexStarGoodsConf::tableName().'.id' => $this->id,
            IndexStarGoodsConf::tableName().'.goods_id' => $this->goods_id,
            IndexStarGoodsConf::tableName().'.tab_id' => $this->tab_id,
            IndexStarGoodsConf::tableName().'.sort_order' => $this->sort_order,
        ]);

        return $dataProvider;
    }
}
