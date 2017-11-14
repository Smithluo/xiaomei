<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_paihang_floor".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property integer $sort_order
 */
class IndexPaihangFloor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_paihang_floor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['sort_order'], 'integer'],
            [['title'], 'string', 'max' => 20],
            ['image', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'description' => '描述文本',
            'image' => '图片',
            'sort_order' => '排序值',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \mongosoft\file\UploadImageBehavior::className(),
                'attribute' => 'image',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/zhifa_paihang_floor/{id}',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/zhifa_paihang_floor/{id}',
                'thumbs' => [],
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->sort_order == null) {
                $this->sort_order = 0;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getPaihangGoods() {
        return $this->hasMany(IndexPaihangGoods::className(), [
            'floor_id' => 'id',
        ])->orderBy([
            'sort_order' => SORT_DESC,
            'id' => SORT_ASC,
        ]);
    }
}
