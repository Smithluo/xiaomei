<?php

namespace common\models;

use common\helper\DateTimeHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ServicerDivideRecordSearch represents the model behind the search form about `common\models\ServicerDivideRecord`.
 */
class ServicerDivideRecordSearch extends ServicerDivideRecord
{
    //order_info
    public $order_sn;
    public $add_time;
    public $consignee;
    public $mobile;
    public $address;
    public $goods_amount;
    public $order_status;
    public $total_divide_amount;
    public $date_added;
    public $date_modified;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),
        [
            'date_added' => '开始时间',
            'date_modified' => '结束时间',
            'order_sn' => '订单号',
            'order_status' => '订单状态',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'spec_strategy_id', 'user_id', 'servicer_user_id', 'parent_servicer_user_id', 'divide_amount', 'parent_divide_amount', 'child_record_id', 'money_in_record_id'], 'integer'],
            [['servicer_user_name'], 'string', 'max' => 255],
            [['amount'], 'number'],
            [['order_sn', 'date_added', 'date_modified', 'servicer_user_name', 'order_sn', 'order_status'], 'safe'],
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

        $query->joinWith('orderInfo');
        $query->joinWith('orderGoods');
        $query->joinWith('user');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['gridPageSize'],
            ],
        ]);

//        $dataProvider->setSort([
//            'attributes' => [
//                'order_sn' => [
//                    'asc' => ['o_order_info.order_sn' => SORT_ASC],
//                    'desc' => ['o_order_info.order_sn' => SORT_DESC],
//                    'label' => '订单编号',
//                ],
//                'add_time' => [
//                    'asc' => ['o_order_info.add_time' => SORT_ASC],
//                    'desc' => ['o_order_info.add_time' => SORT_DESC],
//                    'label' => '下单时间',
//                ],
//                'consignee' => [
//                    'asc' => ['o_order_info.consignee' => SORT_ASC],
//                    'desc' => ['o_order_info.consignee' => SORT_DESC],
//                    'label' => '收货人',
//                ],
//                'mobile' => [
//                    'asc' => ['o_order_info.mobile' => SORT_ASC],
//                    'desc' => ['o_order_info.mobile' => SORT_DESC],
//                    'label' => '收货人电话',
//                ],
//                'goods_amount' => [
//                    'asc' => ['o_order_info.goods_amount' => SORT_ASC],
//                    'desc' => ['o_order_info.goods_amount' => SORT_DESC],
//                    'label' => '订单总金额',
//                ],
//                'total_divide_amount' => [
//                    'asc' => ['(divide_amount + parent_divide_amount)' => SORT_ASC],
//                    'desc' => ['(divide_amount + parent_divide_amount)' => SORT_DESC],
//                    'label' => '订单总提成',
//                ],
//                'servicer_user_name' => [
//                    'asc' => ['servicer_user_name' => SORT_ASC],
//                    'desc' => ['servicer_user_name' => SORT_DESC],
//                    'label' => '业务员',
//                ],
//                'divide_amount' => [
//                    'asc' => ['divide_amount' => SORT_ASC],
//                    'desc' => ['divide_amount' => SORT_DESC],
//                    'label' => '业务员提成',
//                ],
//                'order_status' => [
//                    'asc' => ['o_order_info.order_status' => SORT_ASC],
//                    'desc' => ['o_order_info.order_status' => SORT_DESC],
//                    'label' => '订单状态',
//                ],
//            ]
//        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $timeStart = $this->date_added ? DateTimeHelper::getGMTDateBegin($this->date_added, 'timestamp') : 0;
        $timeEnd = $this->date_modified ? DateTimeHelper::getGMTDateEnd($this->date_modified, 'timestamp') : 0;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'spec_strategy_id' => $this->spec_strategy_id,
            'user_id' => $this->user_id,
            'divide_amount' => $this->divide_amount,
            'parent_divide_amount' => $this->parent_divide_amount,
//            'child_record_id' => $this->child_record_id,
//            'money_in_record_id' => $this->money_in_record_id,
        ]);

        if($this->order_status) {
            $statusFileterWhere = OrderInfo::$order_cs_status[$this->order_status];
            $query->andFilterWhere($statusFileterWhere);
        }

        $query->andFilterWhere(['like', 'o_order_info.order_sn', $this->order_sn]);
        $query->andFilterWhere(['like', 'servicer_user_name', $this->servicer_user_name]);

        //一级服务商，过滤出一级服务商的流水列表
        if (Yii::$app->user->identity['servicer_super_id'] == 0 || Yii::$app->user->identity['servicer_super_id'] == Yii::$app->user->identity['user_id']) {
            $query->andFilterWhere([
                'parent_servicer_user_id' => Yii::$app->user->identity['user_id'],
            ]);
//            $query->andFilterWhere(['not', ['child_record_id'=>0]]);
        }
        //二级服务商，过滤出二级服务商的流水列表
        else {
            $query->andFilterWhere([
                'servicer_user_id' => Yii::$app->user->identity['user_id'],
            ]);
        }

        if($timeStart) {
            $query->andFilterWhere(['>=', 'o_order_info.add_time', $timeStart]);
        }

        if($timeEnd) {
            $query->andFilterWhere(['<=', 'o_order_info.add_time', $timeEnd]);
        }

        return $dataProvider;
    }

    public function searchByOrder($params) {
        $query = OrderInfo::find();

        $query->joinWith('ordergoods ordergoods');
        $query->joinWith('users users');
        $query->joinWith('servicerDivideRecord servicerDivideRecord');

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

        $timeStart = $this->date_added ? DateTimeHelper::getGMTDateBegin($this->date_added, 'timestamp') : 0;
        $timeEnd = $this->date_modified ? DateTimeHelper::getGMTDateEnd($this->date_modified, 'timestamp') : 0;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'spec_strategy_id' => $this->spec_strategy_id,
            'user_id' => $this->user_id,
        ]);

        if($this->order_status) {
            $statusFileterWhere = OrderInfo::$order_cs_status[$this->order_status];
            $query->andFilterWhere($statusFileterWhere);
        }

        $query->andFilterWhere(['like', 'order_sn', $this->order_sn]);
        $query->andFilterWhere(['like', 'servicer_user_name', $this->servicer_user_name]);

        //一级服务商，过滤出一级服务商的流水列表
        if (Yii::$app->user->identity['servicer_super_id'] == 0 || Yii::$app->user->identity['servicer_super_id'] == Yii::$app->user->identity['user_id']) {
            $query->andFilterWhere([
                'servicerDivideRecord.parent_servicer_user_id' => Yii::$app->user->identity['user_id'],
            ]);
//            $query->andFilterWhere(['not', ['child_record_id'=>0]]);
        }
        //二级服务商，过滤出二级服务商的流水列表
        else {
            $query->andFilterWhere([
                'servicerDivideRecord.servicer_user_id' => Yii::$app->user->identity['user_id'],
            ]);
        }

        if($timeStart) {
            $query->andFilterWhere(['>=', 'add_time', $timeStart]);
        }

        if($timeEnd) {
            $query->andFilterWhere(['<=', 'add_time', $timeEnd]);
        }

        $query->orderBy([
            OrderInfo::tableName().'.order_id' => SORT_DESC,
        ]);
        $query->groupBy('order_id');

        return $dataProvider;
    }
}
