<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_zhifa_youxuan".
 *
 * @property integer $id
 * @property string $image
 * @property string $url
 * @property integer $sort_order
 */
class IndexZhifaYouxuan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_zhifa_youxuan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order'], 'integer'],
            [['url'], 'string', 'max' => 255],
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
            'image' => '图片',
            'url' => '跳转链接',
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
                'path' => '@imgRoot/zhifa_youxuan/{id}',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/zhifa_youxuan/{id}',
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
}
