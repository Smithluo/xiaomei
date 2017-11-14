<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ArticleCat;

/**
 * ArticleCatSearch represents the model behind the search form about `common\models\ArticleCat`.
 */
class ArticleCatSearch extends ArticleCat
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'cat_type', 'sort_order', 'show_in_nav', 'parent_id'], 'integer'],
            [['cat_name', 'keywords', 'cat_desc'], 'safe'],
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
        $query = ArticleCat::find();
        $query->joinWith('children children');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100000,
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
            ArticleCat::tableName().'.cat_id' => $this->cat_id,
            ArticleCat::tableName().'.cat_type' => $this->cat_type,
            ArticleCat::tableName().'.sort_order' => $this->sort_order,
            ArticleCat::tableName().'.show_in_nav' => $this->show_in_nav,
            ArticleCat::tableName().'.parent_id' => $this->parent_id,
        ]);

        $query->andFilterWhere(['like', ArticleCat::tableName().'.cat_name', $this->cat_name])
            ->andFilterWhere(['like', ArticleCat::tableName().'.keywords', $this->keywords])
            ->andFilterWhere(['like', ArticleCat::tableName().'.cat_desc', $this->cat_desc]);

        return $dataProvider;
    }
}
