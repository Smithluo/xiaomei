<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Article;

/**
 * ArticleSearch represents the model behind the search form about `common\models\Article`.
 */
class ArticleSearch extends Article
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'article_id', 'cat_id', 'article_type', 'is_open', 'add_time', 'open_type', 'sort_order',
                    'brand_id', 'gallery_id', 'resource_site_id', 'link_cat', 'country'
                ],
                'integer'],
            [
                [
                    'title', 'content', 'author', 'author_email', 'keywords', 'file_url', 'link', 'description',
                    'scene', 'resource_type'
                ],
                'safe'
            ],
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
        $query = Article::find();

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

        //  默认只显示 线上显示的文章
        if (!isset($this->is_open)) {
            $this->is_open = 1;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'article_id' => $this->article_id,
            'cat_id' => $this->cat_id,
            'article_type' => $this->article_type,
            'is_open' => $this->is_open,
            'add_time' => $this->add_time,
            'open_type' => $this->open_type,
            'sort_order' => $this->sort_order,
            'resource_type' => $this->resource_type,
            'gallery_id' => $this->gallery_id,
            'resource_site_id' => $this->resource_site_id,
            'country' => $this->country,
            'link_cat' => $this->link_cat,
            'scene' => $this->scene,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'author_email', $this->author_email])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'file_url', $this->file_url])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }

    public function afterValidate()
    {
        $this->trigger(self::EVENT_AFTER_VALIDATE);
    }
}
