<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Users;

/**
 * UsersSearch represents the model behind the search form about `common\models\Users`.
 */
class UsersSearch extends Users
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'sex', 'pay_points', 'rank_points', 'address_id', 'zone_id', 'reg_time', 'last_login', 'visit_count', 'user_rank', 'is_special', 'parent_id', 'flag', 'is_validated', 'servicer_info_id'], 'integer'],
            [['email', 'user_name', 'password', 'question', 'answer', 'birthday', 'last_time', 'last_ip', 'ec_salt', 'salt', 'alias', 'msn', 'qq', 'office_phone', 'home_phone', 'mobile_phone', 'company_name', 'passwd_question', 'passwd_answer', 'headimgurl', 'openid', 'qq_open_id', 'aite_id', 'unionid', 'wx_pc_openid', 'licence_image', 'auth_key', 'access_token'], 'safe'],
            [['user_money', 'frozen_money', 'credit_line'], 'number'],
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
        $query = Users::find();
        $query->with([
            'userRegion',
        ]);

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
            'servicer_info_id' => $this->servicer_info_id,
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
            ->andFilterWhere(['not', ['servicer_info_id' => 0]])
            ->andFilterWhere(['like', 'access_token', $this->access_token]);

        return $dataProvider;
    }
}
