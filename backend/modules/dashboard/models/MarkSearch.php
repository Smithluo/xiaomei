<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/15
 * Time: 11:20
 */

namespace backend\modules\dashboard\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class MarkSearch extends Mark
{
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

        if (isset($params['page_size']) && $params['page_size'] == 0) {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pagesize' => Mark::find()->count(),
                ],
            ]);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
        ]);

        if (!empty($this->start_time) && !empty($this->end_time)) {
            $query->andFilterWhere([
                'between', 'date', $this->start_time, $this->end_time
            ]);
        }

        if ($this->login_times) {
            $query->andFilterWhere(['>=', 'login_times', $this->login_times]);
        }

        if ($this->click_times) {
            $query->andFilterWhere(['>=', 'click_times', $this->click_times]);
        }

        if ($this->order_count) {
            $query->andFilterWhere(['>=', 'order_count', $this->order_count]);
        }

        if ($this->pay_count) {
            $query->andFilterWhere(['>=', 'pay_count', $this->pay_count]);
        }

        if ($this->plat_form) {
            $query->andFilterWhere(['plat_form' => $this->plat_form]);
        }

        return $dataProvider;
    }
}