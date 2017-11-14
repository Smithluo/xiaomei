<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/3 0003
 * Time: 16:32
 */

namespace service\models;

use brand\models\OrderInfo;
use common\helper\DateTimeHelper;
use common\models\OrderGroup;
use common\models\ServiceUser;
use Yii;
use yii\data\ActiveDataProvider;

class ServiceUserSearch extends ServiceUser
{
    public $date_added;
    public $date_modified;
    public $group_id;
    public $group_status;
    public $id;
    public $mobile;
    public function rules()
    {
        return [
            [['sex', 'pay_points', 'rank_points', 'address_id', 'zone_id', 'reg_time', 'last_login', 'visit_count',
                'user_rank', 'is_special', 'parent_id', 'flag', 'is_validated', 'servicer_user_id', 'servicer_super_id',
                'servicer_info_id', 'servicer_super_id'], 'integer'],
            [['parent_id', 'servicer_super_id', 'bank_info_id'], 'default', 'value'=>0],
            [['birthday', 'last_time','group_id','date_added','date_modifie','group_status','mobile'], 'safe'],
            [['user_money', 'frozen_money', 'credit_line'], 'number'],
//            [['alias', 'msn', 'qq', 'office_phone', 'home_phone', 'mobile_phone', 'company_name', 'credit_line',
//                'qq_open_id', 'unionid', 'wx_pc_openid', 'licence_image'], 'required'],
            [['alias', 'msn', 'qq', 'office_phone', 'home_phone', 'company_name', 'mobile_phone', 'qq_open_id', 'unionid', 'wx_pc_openid', 'licence_image'], 'default', 'value'=>''],
            [['credit_line'], 'default', 'value'=>0],
            [['email', 'alias', 'msn'], 'string', 'max' => 60],
            ['email', 'email'],
            [['user_name', 'question', 'answer', 'company_name', 'passwd_answer', 'headimgurl', 'licence_image',
                'brand_id_list', 'access_token'], 'string', 'max' => 255],
            [['nickname'], 'string', 'max' => 255],
            [['password', 'auth_key'], 'string', 'max' => 32],
            [['last_ip'], 'string', 'max' => 15],
            [['ec_salt', 'salt'], 'string', 'max' => 10],
            [['qq', 'office_phone', 'home_phone', 'mobile_phone'], 'string', 'max' => 20],
            [['passwd_question', 'openid', 'qq_open_id', 'aite_id', 'unionid', 'wx_pc_openid'], 'string', 'max' => 50],
            [['user_name'], 'unique', 'message'=>'用户名已被注册，请重新输入。'],
            [['mobile_phone'], 'unique', 'message'=>'手机号已被注册，请重新输入。'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge([
            'date_added' => '开始时间',
            'date_modified' => '结束时间',
            'group_id' => '订单号',
            'group_status' => '订单状态',
            'mobile' => '收货人电话'
        ],parent::attributeLabels());
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param bool $selfFilter  是否过滤掉自身
     *
     * @return ActiveDataProvider
     */
    public function search($params, $selfFilter = false)
    {
        $query = ServiceUser::find();

        $query->joinWith('servicerUserInfo');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['gridPageSize'],
            ],
        ]);

//        $dataProvider->setSort([
//            'attributes' => [
//                'user_name' => [
//                    'asc' => ['user_name' => SORT_ASC],
//                    'desc' => ['user_name' => SORT_DESC],
//                    'label' => '用户名',
//                ],
//                'mobile_phone' => [
//                    'asc' => ['mobile_phone' => SORT_ASC],
//                    'desc' => ['mobile_phone' => SORT_DESC],
//                    'label' => '电话',
//                ],
//                'servicer_code' => [
//                    'asc' => ['o_servicer_user_info.servicer_code' => SORT_ASC],
//                    'desc' => ['o_servicer_user_info.servicer_code' => SORT_DESC],
//                    'label' => '业务码',
//                ],
////                'divide_amount' => [
////                    'asc' => ['o_order_info.mobile' => SORT_ASC],
////                    'desc' => ['o_order_info.mobile' => SORT_DESC],
////                    'label' => '当前提成余额',
////                ],
//            ]
//        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'sex' => $this->sex,
            'birthday' => $this->birthday,
            'user_money' => $this->user_money,
            'frozen_money' => $this->frozen_money,
            'pay_points' => $this->pay_points,
            'rank_points' => $this->rank_points,
            'address_id' => $this->address_id,
            'zone_id' => $this->zone_id,
            'reg_time' => $this->reg_time,
            'last_login' => $this->last_login,
            'last_time' => $this->last_time,
            'visit_count' => $this->visit_count,
            'user_rank' => $this->user_rank,
            'is_special' => $this->is_special,
            'parent_id' => $this->parent_id,
            'flag' => $this->flag,
            'is_validated' => $this->is_validated,
            'credit_line' => $this->credit_line,
            'servicer_super_id' => Yii::$app->user->identity['user_id'],
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'question', $this->question])
            ->andFilterWhere(['like', 'answer', $this->answer])
            ->andFilterWhere(['like', 'last_ip', $this->last_ip])
            ->andFilterWhere(['like', 'ec_salt', $this->ec_salt])
            ->andFilterWhere(['like', 'salt', $this->salt])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'msn', $this->msn])
            ->andFilterWhere(['like', 'qq', $this->qq])
            ->andFilterWhere(['like', 'office_phone', $this->office_phone])
            ->andFilterWhere(['like', 'home_phone', $this->home_phone])
            ->andFilterWhere(['like', 'mobile_phone', $this->mobile_phone])
            ->andFilterWhere(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'passwd_question', $this->passwd_question])
            ->andFilterWhere(['like', 'passwd_answer', $this->passwd_answer])
            ->andFilterWhere(['like', 'headimgurl', $this->headimgurl])
            ->andFilterWhere(['like', 'openid', $this->openid])
            ->andFilterWhere(['like', 'qq_open_id', $this->qq_open_id])
            ->andFilterWhere(['like', 'aite_id', $this->aite_id])
            ->andFilterWhere(['like', 'unionid', $this->unionid])
            ->andFilterWhere(['like', 'wx_pc_openid', $this->wx_pc_openid])
            ->andFilterWhere(['like', 'licence_image', $this->licence_image])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            //过滤出有服务商信息的
            ->andFilterWhere(['not', ['servicer_info_id' => 0]]);

        if ($selfFilter && Yii::$app->user->identity['user_id']) {
            $query->andWhere(['!=', 'user_id', Yii::$app->user->identity['user_id']]);
        }
        return $dataProvider;
    }

    public function searchByOrderGroup($params)
    {
        $query = OrderGroup::find();
        //总单
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

        //按照总单号来筛选
        if($this->group_id)
        {
            $query->andWhere(['group_id'=>$this->group_id]);
        }

        //按照订单状态来筛选
        if(isset($this->group_status) && $this->group_status!=OrderGroup::ORDER_GROUP_STATUS_ALL)
        {
            $query->andWhere([
                'group_status' => $this->group_status,
            ]);
        }

        if($this->mobile)
        {
            $query->andFilterWhere([
                'like',
                'mobile',
                $this->mobile,
            ]);
        }

        $timeStart = $this->date_added ? DateTimeHelper::getGMTDateBegin($this->date_added, 'timestamp') : 0;
        $timeEnd = $this->date_modified ? DateTimeHelper::getGMTDateEnd($this->date_modified, 'timestamp') : 0;

        if($timeStart) {
            $query->andFilterWhere(['>=', 'create_time', $timeStart]);
        }

        if($timeEnd) {
            $query->andFilterWhere(['<=', 'create_time', $timeEnd]);
        }

        $query->andWhere([
            'user_id'=>$this->id,
        ]);

        $query->all();

        return $dataProvider;
    }
}