<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\IndexStarBrandConf;

/**
 * IndexStarBrandConfSearch represents the model behind the search form about `common\models\IndexStarBrandConf`.
 */
class IndexStarBrandConfSearch extends IndexStarBrandConf
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'tab_id', 'sort_order'], 'integer'],
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
        $query = IndexStarBrandConf::find();
        $query->joinWith(['brand', 'tab']);
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
            IndexStarBrandConf::tableName().'.id' => $this->id,
            IndexStarBrandConf::tableName().'.brand_id' => $this->brand_id,
            IndexStarBrandConf::tableName().'.tab_id' => $this->tab_id,
            IndexStarBrandConf::tableName().'.sort_order' => $this->sort_order,
        ]);

        return $dataProvider;
    }
}
