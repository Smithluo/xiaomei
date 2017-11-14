<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\FullCutRule;

/**
 * FullCutRuleSearch represents the model behind the search form about `backend\models\FullCutRule`.
 */
class FullCutRuleSearch extends FullCutRule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_id', 'event_id', 'status'], 'integer'],
            [['rule_name'], 'safe'],
            [['above', 'cut'], 'number'],
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
        $query = FullCutRule::find();

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
            'event_id' => $this->event_id,
            'above' => $this->above,
            'cut' => $this->cut,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'rule_name', $this->rule_name])->orderBy(['rule_id' => SORT_DESC]);

        return $dataProvider;
    }
}
