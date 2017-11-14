<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\GiftPkg;

/**
 * GiftPkgSearch represents the model behind the search form about `backend\models\GiftPkg`.
 */
class GiftPkgSearch extends GiftPkg
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'shipping_code', 'is_on_sale', 'updated_by'], 'integer'],
            [['name', 'img', 'brief', 'updated_at', 'pkg_desc'], 'safe'],
            [['price'], 'number'],
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
        $query = GiftPkg::find();

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
            'price' => $this->price,
            'shipping_code' => $this->shipping_code,
            'is_on_sale' => $this->is_on_sale,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'img', $this->img])
            ->andFilterWhere(['like', 'brief', $this->brief])
            ->andFilterWhere(['like', 'pkg_desc', $this->pkg_desc]);

        return $dataProvider;
    }
}
