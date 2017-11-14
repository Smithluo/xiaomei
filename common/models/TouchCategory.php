<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_touch_category".
 *
 * @property string $id
 * @property string $cat_id
 * @property string $cat_image
 */
class TouchCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_touch_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id'], 'integer'],
            [['cat_image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cat_id' => 'Cat ID',
            'cat_image' => 'Cat Image',
        ];
    }
}
