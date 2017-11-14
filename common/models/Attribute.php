<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_attribute".
 *
 * @property integer $attr_id
 * @property integer $cat_id
 * @property string $attr_name
 * @property integer $attr_input_type
 * @property integer $attr_type
 * @property string $attr_values
 * @property integer $attr_index
 * @property integer $sort_order
 * @property integer $is_linked
 * @property integer $attr_group
 */
class Attribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'attr_input_type', 'attr_type', 'attr_index', 'sort_order', 'is_linked', 'attr_group'], 'integer'],
            [['attr_values'], 'required'],
            [['attr_values'], 'string'],
            [['attr_name'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'attr_id' => '属性ID',
            'cat_id' => '商品属性ID',
            'attr_name' => '属性名',
            'attr_input_type' => '该属性值的录入方式',
            'attr_type' => '属性是否可选',
            'attr_values' => '可选值列表',
            'attr_index' => '是否需要检索',
            'sort_order' => '排序',
            'is_linked' => '相同属性值的商品是否关联？	',
            'attr_group' => '属性分组',
        ];
    }

    public function getGoodsType() {
        return $this->hasOne(GoodsType::className(), ['cat_id' => 'cat_id']);
    }

    public function inputTypeString() {
        switch($this->attr_input_type) {
            case 0:
                return '手工录入';
            case 1:
                return '按行输入';
            case 2:
                return '多行文本框';
        }
    }

    public function attrTypeString() {
        switch($this->attr_type) {
            case 0:
                return '唯一';
            case 1:
                return '单选';
            case 2:
                return '复选';
        }
    }
}
