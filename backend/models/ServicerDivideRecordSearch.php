<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ServicerDivideRecord;

/**
 * ServicerDivideRecordSearch represents the model behind the search form about `backend\models\ServicerDivideRecord`.
 */
class ServicerDivideRecordSearch extends ServicerDivideRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'user_id', 'servicer_user_id', 'parent_servicer_user_id', 'child_record_id', 'money_in_record_id'], 'integer'],
            [['amount', 'divide_amount', 'parent_divide_amount'], 'number'],
            [['spec_strategy_id', 'servicer_user_name'], 'safe'],
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
        $query = ServicerDivideRecord::find();
        $query->with('orderInfo');
        $query->with('orderInfo.ordergoods');
        $query->with('orderInfo.ordergoods.goods');
        $query->with('user');
        $query->with('servicer');
        $query->with('parentServicer');

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
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'servicer_user_id' => $this->servicer_user_id,
            'parent_servicer_user_id' => $this->parent_servicer_user_id,
            'child_record_id' => $this->child_record_id,
//            'money_in_record_id' => $this->money_in_record_id,
        ]);

        $query->andFilterWhere(['like', 'spec_strategy_id', $this->spec_strategy_id])
            ->andFilterWhere(['like', 'servicer_user_name', $this->servicer_user_name])
            ->andFilterWhere(['>', 'amount', $this->amount])
            ->andFilterWhere(['>', 'divide_amount', $this->divide_amount])
            ->andFilterWhere(['>', 'parent_divide_amount', $this->parent_divide_amount])
        ;

        return $dataProvider;
    }
}
