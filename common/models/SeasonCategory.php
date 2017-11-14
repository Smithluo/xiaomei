<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_season_category".
 *
 * @property integer $id
 * @property string $title
 * @property integer $sort_order
 */
class SeasonCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_season_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order'], 'integer'],
            [['title'], 'string', 'max' => 20],
            [['sort_order', 'title'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '分类名',
            'sort_order' => '排序',
        ];
    }

    public function getSeasonGoods()
    {
        return $this->hasMany(SeasonGoods::className(), [
            'type' => 'id'
        ])->orderBy([
            SeasonGoods::tableName().'.sort_order' => SORT_DESC
        ]);
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        SeasonGoods::deleteAll([
            'type' => $this->id,
        ]);

        return true;
    }
}
