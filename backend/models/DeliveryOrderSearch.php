<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\DeliveryOrder;

/**
 * DeliveryOrderSearch represents the model behind the search form about `backend\models\DeliveryOrder`.
 */
class DeliveryOrderSearch extends DeliveryOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_id', 'order_id', 'add_time', 'shipping_id', 'user_id', 'country', 'province', 'city', 'district', 'update_time', 'suppliers_id', 'status', 'agency_id'], 'integer'],
            [['delivery_sn', 'order_sn', 'invoice_no', 'shipping_name', 'action_user', 'consignee', 'address', 'sign_building', 'email', 'zipcode', 'tel', 'mobile', 'best_time', 'postscript', 'how_oos', 'group_id'], 'safe'],
            [['insure_fee', 'shipping_fee'], 'number'],
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
        $query = DeliveryOrder::find();

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
            'delivery_id' => $this->delivery_id,
            'order_id' => $this->order_id,
            'add_time' => $this->add_time,
            'shipping_id' => $this->shipping_id,
            'user_id' => $this->user_id,
            'country' => $this->country,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'insure_fee' => $this->insure_fee,
            'shipping_fee' => $this->shipping_fee,
            'update_time' => $this->update_time,
            'suppliers_id' => $this->suppliers_id,
            'status' => $this->status,
            'agency_id' => $this->agency_id,
        ]);

        $query->andFilterWhere(['like', 'delivery_sn', $this->delivery_sn])
            ->andFilterWhere(['like', 'order_sn', $this->order_sn])
            ->andFilterWhere(['like', 'invoice_no', $this->invoice_no])
            ->andFilterWhere(['like', 'shipping_name', $this->shipping_name])
            ->andFilterWhere(['like', 'action_user', $this->action_user])
            ->andFilterWhere(['like', 'consignee', $this->consignee])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'sign_building', $this->sign_building])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'zipcode', $this->zipcode])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'best_time', $this->best_time])
            ->andFilterWhere(['like', 'postscript', $this->postscript])
            ->andFilterWhere(['like', 'group_id', $this->group_id])
            ->andFilterWhere(['like', 'how_oos', $this->how_oos]);

        return $dataProvider;
    }
}
