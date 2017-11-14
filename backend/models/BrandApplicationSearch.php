<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use home\models\BrandApplication;

/**
 * BrandApplicationSearch represents the model behind the search form about `home\models\BrandApplication`.
 */
class BrandApplicationSearch extends BrandApplication
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['company_name', 'company_address', 'name', 'position', 'contact', 'brands', 'licence', 'recorded', 'registed', 'taxed', 'checked'], 'safe'],
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
        $query = BrandApplication::find();

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
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'company_address', $this->company_address])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'contact', $this->contact])
            ->andFilterWhere(['like', 'brands', $this->brands])
            ->andFilterWhere(['like', 'licence', $this->licence])
            ->andFilterWhere(['like', 'recorded', $this->recorded])
            ->andFilterWhere(['like', 'registed', $this->registed])
            ->andFilterWhere(['like', 'taxed', $this->taxed])
            ->andFilterWhere(['like', 'checked', $this->checked]);

        return $dataProvider;
    }
}
