<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SuperPkg;

/**
 * SuperPkgSearch represents the model behind the search form about `common\models\SuperPkg`.
 */
class SuperPkgSearch extends SuperPkg
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gift_pkg_id', 'sort_order', 'start_time', 'end_time'], 'integer'],
            [['pag_name', 'pag_desc'], 'safe'],
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
        $query = SuperPkg::find();

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
            'gift_pkg_id' => $this->gift_pkg_id,
            'sort_order' => $this->sort_order,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        $query->andFilterWhere(['like', 'pag_name', $this->pag_name])
            ->andFilterWhere(['like', 'pag_desc', $this->pag_desc]);

        return $dataProvider;
    }
}
