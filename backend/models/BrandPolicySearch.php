<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BrandPolicy;

/**
 * BrandPolicySearch represents the model behind the search form about `common\models\BrandPolicy`.
 */
class BrandPolicySearch extends BrandPolicy
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'sort_order', 'status'], 'integer'],
            [['policy_content', 'policy_link'], 'safe'],
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
        $query = BrandPolicy::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=> [
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'policy_content', $this->policy_content])
            ->andFilterWhere(['like', 'policy_link', $this->policy_link]);

        return $dataProvider;
    }
}
