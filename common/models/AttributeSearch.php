<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Attribute;

/**
 * AttributeSearch represents the model behind the search form about `common\models\Attribute`.
 */
class AttributeSearch extends Attribute
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attr_id', 'cat_id', 'attr_input_type', 'attr_type', 'attr_index', 'sort_order', 'is_linked', 'attr_group'], 'integer'],
            [['attr_name', 'attr_values'], 'safe'],
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
        $query = Attribute::find();
        $query->joinWith('goodsType');
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
            'attr_id' => $this->attr_id,
            'o_attribute.cat_id' => $this->cat_id,
            'attr_input_type' => $this->attr_input_type,
            'attr_type' => $this->attr_type,
            'attr_index' => $this->attr_index,
            'sort_order' => $this->sort_order,
            'is_linked' => $this->is_linked,
            'attr_group' => $this->attr_group,
        ]);

        $query->andFilterWhere(['like', 'attr_name', $this->attr_name])
            ->andFilterWhere(['like', 'attr_values', $this->attr_values]);

        return $dataProvider;
    }
}
