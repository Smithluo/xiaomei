<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Event;

/**
 * EventSearch represents the model behind the search form about `common\models\Event`.
 */
class EventSearch extends Event
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'event_type', 'pkg_id', 'rule_id', 'start_time', 'end_time', 'updated_at', 'updated_by', 'is_active'], 'integer'],
            [['event_name', 'event_desc', '子类别'], 'safe'],
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
        $query = Event::find();

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

        //  默认显示当前生效的互动
        if (!isset($this->is_active)) {
            $this->is_active = 1;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'event_id' => $this->event_id,
            'event_type' => $this->event_type,
            'pkg_id' => $this->pkg_id,
            'rule_id' => $this->rule_id,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'event_name', $this->event_name])
            ->andFilterWhere(['like', 'event_desc', $this->event_desc])
            ->andFilterWhere(['>=', 'start_time', $this->start_time])
            ->andFilterWhere(['<=', 'end_time', $this->end_time])
            ->orderBy(['event_id' => SORT_DESC]);

        return $dataProvider;
    }
}
