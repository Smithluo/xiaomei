<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GoodsPkg;

/**
 * GoodsPkgSearch represents the model behind the search form about `common\models\GoodsPkg`.
 */
class GoodsPkgSearch extends GoodsPkg
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pkg_id', 'updated_at'], 'integer'],
            [['pkg_name', 'allow_goods_list', 'deny_goods_list'], 'safe'],
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
        $query = GoodsPkg::find();

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
            'pkg_id' => $this->pkg_id,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'pkg_name', $this->pkg_name])
            ->andFilterWhere(['like', 'allow_goods_list', $this->allow_goods_list])
            ->andFilterWhere(['like', 'deny_goods_list', $this->deny_goods_list]);

        return $dataProvider;
    }
}
