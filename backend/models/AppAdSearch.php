<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AppAd;

/**
 * AppAdSearch represents the model behind the search form about `common\models\AppAd`.
 */
class AppAdSearch extends AppAd
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position_id', 'enable', 'sort_order'], 'integer'],
            [['title', 'desc', 'start_time', 'end_time', 'image', 'route', 'params'], 'safe'],
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
        $query = AppAd::find();

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
            'position_id' => $this->position_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'enable' => $this->enable,
            'sort_order' => $this->sort_order,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'desc', $this->desc])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'params', $this->params]);

        return $dataProvider;
    }
}
