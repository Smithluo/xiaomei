<?php

namespace brand\models;

use common\models\CashRecord;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\helper\DateTimeHelper;

/**
 * CashRecordSearch represents the model behind the search form about `brand\models\CashRecord`.
 */
class CashRecordSearch extends CashRecord
{
    public $order_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status'], 'integer'],
            [['cash', 'balance'], 'number'],
            [['note', 'pay_time', 'created_time'], 'safe'],
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
        $query = CashRecord::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['gridPageSize'],
            ],
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
            'cash' => $this->cash,
            'user_id' => $this->user_id,
            'pay_time' => $this->pay_time,
            'created_time' => $this->created_time,
            'status' => $this->status,
            'balance' => $this->balance,
        ]);

        if (isset($params['user_id']) && $params['user_id']) {
            $query->andFilterWhere(['user_id' => $params['user_id']]);
        }

        if (isset($params['cash_status']) && $params['cash_status']) {
            if ($params['cash_status'] == CashRecord::CASH_RECORD_TYPE_IN) {
                $query->andFilterWhere(['>', 'cash', 0]);
            } elseif ($params['cash_status'] == CashRecord::CASH_RECORD_TYPE_OUT) {
                $query->andFilterWhere(['<', 'cash', 0]);
            }

        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            //  考虑没有点击查询的条件
            $start_date = DateTimeHelper::getFormatGMTDateTime($params['start_date']);
            $end_date = DateTimeHelper::getFormatGMTDateTime($params['end_date'].' 23:59:59');
            $query->andFilterWhere(['between', 'created_time', $start_date, $end_date]);
        }

        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
