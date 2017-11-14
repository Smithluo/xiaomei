<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Payment;

/**
 * PaymentSearch represents the model behind the search form about `common\models\Payment`.
 */
class PaymentSearch extends Payment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_id', 'pay_order', 'enabled', 'is_cod', 'is_online'], 'integer'],
            [['pay_code', 'pay_name', 'pay_fee', 'pay_desc', 'pay_config'], 'safe'],
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
        $query = Payment::find();

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
            'pay_id' => $this->pay_id,
            'pay_order' => $this->pay_order,
            'enabled' => $this->enabled,
            'is_cod' => $this->is_cod,
            'is_online' => $this->is_online,
        ]);

        $query->andFilterWhere(['like', 'pay_code', $this->pay_code])
            ->andFilterWhere(['like', 'pay_name', $this->pay_name])
            ->andFilterWhere(['like', 'pay_fee', $this->pay_fee])
            ->andFilterWhere(['like', 'pay_desc', $this->pay_desc])
            ->andFilterWhere(['like', 'pay_config', $this->pay_config]);

        return $dataProvider;
    }
}
