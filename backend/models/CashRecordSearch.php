<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CashRecord;

/**
 * CashRecordSearch represents the model behind the search form about `common\models\CashRecord`.
 */
class CashRecordSearch extends CashRecord
{
    public $type = 0;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type'], 'integer'],
            [['cash', 'balance'], 'number'],
            [['note', 'pay_time', 'created_time', 'type'], 'safe'],
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
            'balance' => $this->balance,
        ]);

        if ($this->type == 0) {
            $query->andFilterWhere(['<', 'cash', 0]);
        }
        elseif ($this->type == 1) {
            $query->andFilterWhere(['>=', 'cash', 0]);
        }
        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
