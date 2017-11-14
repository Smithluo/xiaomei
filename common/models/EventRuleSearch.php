<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EventRule;

/**
 * EventRuleSearch represents the model behind the search form about `common\models\EventRule`.
 */
class EventRuleSearch extends EventRule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_id', 'match_type', 'match_value', 'match_effect', 'gift_id', 'gift_num', 'updated_at'], 'integer'],
            [['rule_name'], 'safe'],
            [['gift_show_peice', 'gift_need_pay'], 'number'],
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
        $query = EventRule::find();

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
            'rule_id' => $this->rule_id,
            'match_type' => $this->match_type,
            'match_value' => $this->match_value,
            'match_effect' => $this->match_effect,
            'gift_id' => $this->gift_id,
            'gift_num' => $this->gift_num,
            'gift_show_peice' => $this->gift_show_peice,
            'gift_need_pay' => $this->gift_need_pay,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'rule_name', $this->rule_name])->orderBy(['rule_id' => SORT_DESC]);;

        return $dataProvider;
    }
}
