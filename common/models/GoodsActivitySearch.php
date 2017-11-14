<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GoodsActivity;

/**
 * GoodsActivitySearch represents the model behind the search form about `common\models\GoodsActivity`.
 */
class GoodsActivitySearch extends GoodsActivity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'act_id', 'act_type', 'goods_id', 'start_num', 'limit_num', 'match_num', 'product_id',
                    'start_time', 'end_time', 'is_hot', 'is_finished', 'order_expired_time'
                ],
                'integer'
            ],
            [
                [
                    'act_name', 'act_desc', 'production_date', 'show_banner', 'qr_code', 'goods_name',
                    'goods_list', 'ext_info', 'shipping_code'
                ],
                'safe'
            ],
            [['old_price', 'act_price'], 'number'],
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
        $query = GoodsActivity::find();

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
            'act_id' => $this->act_id,
            'act_type' => $this->act_type,
            'goods_id' => $this->goods_id,
            'start_num' => $this->start_num,
            'limit_num' => $this->limit_num,
            'match_num' => $this->match_num,
            'old_price' => $this->old_price,
            'act_price' => $this->act_price,
            'production_date' => $this->production_date,
            'product_id' => $this->product_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_hot' => $this->is_hot,
            'is_finished' => $this->is_finished,
        ]);

        $query->andFilterWhere(['like', 'act_name', $this->act_name])
            ->andFilterWhere(['like', 'act_desc', $this->act_desc])
            ->andFilterWhere(['like', 'show_banner', $this->show_banner])
            ->andFilterWhere(['like', 'qr_code', $this->qr_code])
            ->andFilterWhere(['like', 'goods_name', $this->goods_name])
            ->andFilterWhere(['like', 'goods_list', $this->goods_list])
            ->andFilterWhere(['like', 'ext_info', $this->ext_info])
            ->orderBy(['act_id' => SORT_DESC]);

        return $dataProvider;
    }
}
