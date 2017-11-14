<?php

namespace service\models;

use backend\models\OrderInfo;
use common\helper\DateTimeHelper;
use common\models\Users;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OrderGroup;
use yii\web\ForbiddenHttpException;

/**
 * OrderGroupSearch represents the model behind the search form about `common\models\OrderGroup`.
 */
class ServiceOrderGroupSearch extends OrderGroup
{
    public $order_sn;
    public $add_time;
    public $order_status;
    public $total_divide_amount;
    public $date_added;
    public $date_modified;
    public $servicer_user_name;
    public $nickname;
    public $consignee;
    public $citycode;
    public $mobile;
    /**
     * @inheritdoc //验证规则
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'create_time', 'group_status', 'country', 'province', 'city', 'district', 'pay_id', 'pay_time', 'shipping_time', 'recv_time'], 'integer'],
            [['group_id', 'consignee', 'address', 'mobile', 'pay_name', 'nickname','date_added','date_modified' ,'consignee' , 'citycode','mobile'], 'safe'],
            [['goods_amount', 'shipping_fee', 'money_paid', 'order_amount', 'discount'], 'number'],
        ];
    }
    public function attributeLabels()
    {
        return array_merge([
            'nickname'=>'业务员',
            'date_added'=>'开始时间',
            'date_modified'=>'结束时间'
        ],parent::attributeLabels());
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
//    public function search($params)
//    {
//        $query = OrderGroup::find();
//
//        // add conditions that should always apply here
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//        ]);
//
//        $this->load($params);
//
//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }
//
//        // grid filtering conditions
//        $query->andFilterWhere([
//            'id' => $this->id,
//            'user_id' => $this->user_id,
//            'create_time' => $this->create_time,
//            'group_status' => $this->group_status,
//            'country' => $this->country,
//            'province' => $this->province,
//            'city' => $this->city,
//            'district' => $this->district,
//            'pay_id' => $this->pay_id,
//            'goods_amount' => $this->goods_amount,
//            'shipping_fee' => $this->shipping_fee,
//            'money_paid' => $this->money_paid,
//            'order_amount' => $this->order_amount,
//            'pay_time' => $this->pay_time,
//            'shipping_time' => $this->shipping_time,
//            'recv_time' => $this->recv_time,
//            'discount' => $this->discount,
//        ]);
//
//        $query->andFilterWhere(['like', 'group_id', $this->group_id])
//            ->andFilterWhere(['like', 'consignee', $this->consignee])
//            ->andFilterWhere(['like', 'address', $this->address])
//            ->andFilterWhere(['like', 'mobile', $this->mobile])
//            ->andFilterWhere(['like', 'pay_name', $this->pay_name]);
//
//        return $dataProvider;
//    }

    public function searchByOrderGroup($params)
    {   //orderGroup
        $query = OrderGroup::find();

        $query->joinWith([
            'users users',
            'users.servicerUser servicerUser',
            'users.servicerUser.supserServicerUser superServicerUser',
            'provinceRegion province',
            'cityRegion city',
            'districtRegion district',
            'orders orders',
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['gridPageSize'],
            ],
        ]);

        $this->load($params);
        //验证
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            OrderGroup::tableName().'.offline' => 0,
        ]);

        $query->andWhere([
            'not',
            ['orders.extension_code' => OrderInfo::EXTENSION_CODE_INTEGRAL],
        ]);

        $timeStart = $this->date_added ? DateTimeHelper::getGMTDateBegin($this->date_added, 'timestamp') : 0;
        $timeEnd = $this->date_modified ? DateTimeHelper::getGMTDateEnd($this->date_modified, 'timestamp') : 0;

        if($this->group_status != OrderGroup::ORDER_GROUP_STATUS_ALL)
        {
            $query->andFilterWhere([
                    'group_status'=>$this->group_status,
                ]
            );
        }
        // 总单号 筛选
        if($this->group_id)
        {
            $query->andFilterWhere(['o_order_group.group_id' => $this->group_id]);
        }
        // 业务员筛选
        if($this->nickname)
        {
            $query->andFilterWhere(['like', 'servicerUser.nickname', $this->nickname]);
        }
        //收货人
        if($this->consignee)
        {
            $query->andFilterWhere(['like', 'o_order_group.consignee', $this->consignee]);
        }
        //用户所在区域
        if($this->citycode)
        {
            $query->andFilterWhere(['users.city' => $this->citycode]);
        }
        if($this->mobile)
        {
            $query->andFilterWhere(['like', 'o_order_group.mobile', $this->mobile]);
        }
        //起止时间 筛选
        if($timeStart) {
            $query->andFilterWhere(['>=', 'create_time', $timeStart]);
        }

        if($timeEnd) {
            $query->andFilterWhere(['<=', 'create_time', $timeEnd]);
        }
        // 只筛选跟本服务商相关的订单
        if(Yii::$app->user->can('service_boss')){
            $query->andFilterWhere(['superServicerUser.user_id' => Yii::$app->user->identity['user_id']]);
        }
        // 经理需要看到对应服务商的订单
        elseif (Yii::$app->user->can('service_manager')) {
            $query->andFilterWhere([
                'superServicerUser.user_id' => Yii::$app->user->identity['servicer_super_id'] ]);
        }
        // 业务员的
        elseif (Yii::$app->user->can('service_saleman')) {
            $query->andFilterWhere(['servicerUser.user_id' => Yii::$app->user->identity['user_id']]);
        }
        // 没有权限
        else {
            throw new ForbiddenHttpException('缺少权限');
        }

        $query->orderBy(['id'=>SORT_DESC]);

        return $dataProvider;
    }
}
