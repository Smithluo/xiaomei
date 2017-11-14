<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\GalleryImg;

/**
 * GalleryImgSearch represents the model behind the search form about `backend\models\GalleryImg`.
 */
class GalleryImgSearch extends GalleryImg
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['img_id', 'gallery_id', 'sort_order'], 'integer'],
            [['img_url', 'img_original', 'img_desc'], 'safe'],
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
        $query = GalleryImg::find();

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
            'img_id' => $this->img_id,
            'gallery_id' => $this->gallery_id,
            'sort_order' => $this->sort_order,
        ]);

        $query->andFilterWhere(['like', 'img_url', $this->img_url])
            ->andFilterWhere(['like', 'img_original', $this->img_original])
            ->andFilterWhere(['like', 'img_desc', $this->img_desc])
            ->orderBy(['img_id' => SORT_DESC]);

        return $dataProvider;
    }
}
