<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Ad;

/**
 * AdSearch represents the model behind the search form about `common\models\Ad`.
 */
class AdSearch extends Ad
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ad_id', 'position_id', 'media_type', 'start_time', 'end_time', 'click_count', 'enabled'], 'integer'],
            [['ad_name', 'ad_link', 'ad_code', 'link_man', 'link_email', 'link_phone'], 'safe'],
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
        $query = Ad::find();
        $query->joinWith('adPosition');

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
            'ad_id' => $this->ad_id,
            'o_ad.position_id' => $this->position_id,
            'media_type' => $this->media_type,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'click_count' => $this->click_count,
            'enabled' => $this->enabled,
        ]);

        $query->andFilterWhere(['like', 'ad_name', $this->ad_name])
            ->andFilterWhere(['like', 'ad_link', $this->ad_link])
            ->andFilterWhere(['like', 'ad_code', $this->ad_code])
            ->andFilterWhere(['like', 'link_man', $this->link_man])
            ->andFilterWhere(['like', 'link_email', $this->link_email])
            ->andFilterWhere(['like', 'link_phone', $this->link_phone]);

        return $dataProvider;
    }
}
