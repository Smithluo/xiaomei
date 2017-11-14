<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\IndexStarGoodsTabConf;

/**
 * IndexStarGoodsTabConfSearch represents the model behind the search form about `common\models\IndexStarGoodsTabConf`.
 */
class IndexStarGoodsTabConfSearch extends IndexStarGoodsTabConf
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sort_order'], 'integer'],
            [['tab_name'], 'safe'],
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
        $query = IndexStarGoodsTabConf::find();

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
            IndexStarGoodsTabConf::tableName().'.id' => $this->id,
            IndexStarGoodsTabConf::tableName().'.sort_order' => $this->sort_order,
        ]);

        $query->andFilterWhere(['like', 'tab_name', $this->tab_name]);

        return $dataProvider;
    }
}
