<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use brand\models\BrandDivideRecord;

/**
 * BrandDivideRecordSearch represents the model behind the search form about `brand\models\BrandDivideRecord`.
 */
class BrandDivideRecordSearch extends BrandDivideRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'brand_id', 'user_id', 'cash_record_id', 'status'], 'integer'],
            [['goods_amount', 'shipping_fee', 'divide_amount'], 'number'],
            [['created_at'], 'safe'],
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
        $query = BrandDivideRecord::find();

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
            'brand_id' => $this->brand_id,
            'goods_amount' => $this->goods_amount,
            'shipping_fee' => $this->shipping_fee,
            'user_id' => $this->user_id,
            'divide_amount' => $this->divide_amount,
            'cash_record_id' => $this->cash_record_id,
            'created_at' => $this->created_at,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
