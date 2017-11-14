<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Category;

/**
 * CategorySearch represents the model behind the search form about `backend\models\Category`.
 */
class CategorySearch extends Category
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'parent_id', 'sort_order', 'show_in_nav', 'is_show', 'grade', 'album_id'], 'integer'],
            [['cat_name', 'keywords', 'cat_desc', 'template_file', 'style', 'filter_attr', 'brand_list'], 'safe'],
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
        $query = Category::find()->indexBy('cat_id');

//        $query->select([Category::tableName().'.*','count(o_goods.goods_id) as goodsCount', 'count(o_goods_cat.goods_id) as goodsCatCount'])->joinWith('goods')->joinWith('goodsCat')->groupBy(Category::tableName().'.cat_id');

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
            'cat_id' => $this->cat_id,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'show_in_nav' => $this->show_in_nav,
            'is_show' => $this->is_show,
            'grade' => $this->grade,
            'album_id' => $this->album_id,
        ]);

        $query->andFilterWhere(['like', 'cat_name', $this->cat_name])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'cat_desc', $this->cat_desc])
            ->andFilterWhere(['like', 'template_file', $this->template_file])
            ->andFilterWhere(['like', 'style', $this->style])
            ->andFilterWhere(['like', 'filter_attr', $this->filter_attr])
            ->andFilterWhere(['like', 'brand_list', $this->brand_list]);

        return $dataProvider;
    }
}
