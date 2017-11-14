<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_goods_type".
 *
 * @property integer $cat_id
 * @property string $cat_name
 * @property integer $enabled
 * @property string $attr_group
 */
class GoodsType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled'], 'integer'],
//            [['attr_group'], 'required'],
            [['cat_name'], 'string', 'max' => 60],
            [['attr_group'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cat_id' => 'Cat ID',
            'cat_name' => 'Cat Name',
            'enabled' => 'Enabled',
            'attr_group' => 'Attr Group',
        ];
    }
}
