<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GoodsGallery;

/**
 * GoodsGallerySearch represents the model behind the search form about `common\models\GoodsGallery`.
 */
class GoodsGallerySearch extends GoodsGallery
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['img_id', 'goods_id'], 'integer'],
            [['img_url', 'img_desc', 'thumb_url', 'img_original'], 'safe'],
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
        $query = GoodsGallery::find();

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
            'goods_id' => $this->goods_id,
        ]);

        $query->andFilterWhere(['like', 'img_url', $this->img_url])
            ->andFilterWhere(['like', 'img_desc', $this->img_desc])
            ->andFilterWhere(['like', 'thumb_url', $this->thumb_url])
            ->andFilterWhere(['like', 'img_original', $this->img_original]);

        return $dataProvider;
    }
}
