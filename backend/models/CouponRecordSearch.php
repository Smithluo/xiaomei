<?php

namespace backend\models;

use common\helper\DateTimeHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CouponRecordSearch represents the model behind the search form about `backend\models\CouponRecord`.
 */
class CouponRecordSearch extends CouponRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coupon_id', 'event_id', 'rule_id', 'received_at', 'used_at', 'status'], 'integer'],
            [['coupon_sn', 'user_id'], 'safe'],
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
        $query = CouponRecord::find();

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
            'coupon_id' => $this->coupon_id,
            'event_id' => $this->event_id,
            'rule_id' => $this->rule_id,
            'received_at' => $this->received_at,
            'used_at' => $this->used_at,
            'user_id' => $this->user_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'coupon_sn', $this->coupon_sn]);

        return $dataProvider;
    }

    public function export($params) {
        $query = CouponRecord::find()->with([
            'user',
            'event',
            'fullCutRule',
            'orderGroup',
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'coupon_id' => $this->coupon_id,
            'event_id' => $this->event_id,
            'rule_id' => $this->rule_id,
            'received_at' => $this->received_at,
            'used_at' => $this->used_at,
            'user_id' => $this->user_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'coupon_sn', $this->coupon_sn]);
        $query->andFilterWhere([
            '>',
            'user_id',
            0,
        ]);

        $couponList = $query->all();

        \moonland\phpexcel\Excel::export([
            'format' => 'Excel5',
            'fileName' => '优惠券'. ($this->rule_id ?: ''),
            'models' => $couponList,
            'columns' => [
                'coupon_id',
                [
                    'attribute' => 'event_id',
                    'value' => function ($model) {
                        if (empty($model->event)) {
                            return '';
                        }
                        return $model->event->event_name;
                    }
                ],
                [
                    'attribute' => 'rule_id',
                    'value' => function ($model) {
                        if (empty($model->fullCutRule)) {
                            return '';
                        }
                        return $model->fullCutRule->rule_name;
                    }
                ],
                [
                    'attribute' => 'user_id',
                    'value' => function ($model) {
                        if (empty($model->user)) {
                            return '';
                        }
                        return $model->user->showName. '('. $model->user->mobile_phone. ')';
                    }
                ],
                [
                    'attribute' => 'received_at',
                    'value' => function ($model) {
                        return DateTimeHelper::getFormatCNDateTime($model->received_at);
                    }
                ],
                [
                    'attribute' => 'used_at',
                    'value' => function ($model) {
                        return DateTimeHelper::getFormatCNDateTime($model->used_at);
                    }
                ],
                'group_id',
                [
                    'attribute' => 'totalAmount',
                    'value' => function ($model) {
                        if (empty($model['orderGroup'])) {
                            return '';
                        }
                        return $model['orderGroup']['goods_amount'] + $model['orderGroup']['shipping_fee'] - $model['orderGroup']['discount'];
                    }
                ],
                [
                    'attribute' => 'group_status',
                    'value' => function ($model) {
                        if (empty($model['orderGroup'])) {
                            return '';
                        }
                        return OrderGroup::$order_group_status[$model['orderGroup']['group_status']];
                    }
                ],
            ], //without header working, because the header will be get label from attribute label.
            'headers' => [
                'coupon_id' => '优惠券ID',
                'event_id' => '活动名称',
                'rule_id' => '活动规则',
                'user_id' => '领取用户',
                'received_at' => '领取时间',
                'used_at' => '消费时间',
                'group_id' => '消费总单号',
                'totalAmount' => '订单总金额(使用优惠券后的价格)',
                'group_status' => '订单状态',
            ],
        ]);
    }
}
