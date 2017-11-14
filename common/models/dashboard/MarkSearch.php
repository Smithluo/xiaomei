<?php

namespace common\models\dashboard;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\dashboard\Mark;

/**
 * MarkSearch represents the model behind the search form about `common\models\dashboard\Mark`.
 */
class MarkSearch extends Mark
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'login_times', 'click_times', 'order_count', 'pay_count'], 'integer'],
            [['plat_form'], 'string'],
            [['date'], 'safe'],
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
        $query = Mark::find();

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
            'date' => $this->date,
            'user_id' => $this->user_id,
            'plat_form' => $this->plat_form,
            'login_times' => $this->login_times,
            'click_times' => $this->click_times,
            'order_count' => $this->order_count,
            'pay_count' => $this->pay_count,
        ]);

        return $dataProvider;
    }
}
