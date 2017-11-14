<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Feedback;

/**
 * FeedbackSearch represents the model behind the search form about `common\models\Feedback`.
 */
class FeedbackSearch extends Feedback
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['msg_id', 'parent_id', 'user_id', 'msg_type', 'msg_status', 'msg_time', 'order_id', 'msg_area'], 'integer'],
            [['user_name', 'user_email', 'msg_title', 'msg_content', 'message_img', 'user_phone'], 'safe'],
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
        $query = Feedback::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=> [
                    'msg_time' => SORT_DESC,
                ]
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
            'msg_id' => $this->msg_id,
            'parent_id' => $this->parent_id,
            'user_id' => $this->user_id,
            'msg_type' => $this->msg_type,
            'msg_status' => $this->msg_status,
            'msg_time' => $this->msg_time,
            'order_id' => $this->order_id,
            'msg_area' => $this->msg_area,
        ]);

        $query->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'user_email', $this->user_email])
            ->andFilterWhere(['like', 'msg_title', $this->msg_title])
            ->andFilterWhere(['like', 'msg_content', $this->msg_content])
            ->andFilterWhere(['like', 'message_img', $this->message_img])
            ->andFilterWhere(['like', 'user_phone', $this->user_phone]);

        return $dataProvider;
    }
}
