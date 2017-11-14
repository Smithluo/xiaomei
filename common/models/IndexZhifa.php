<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_zhifa".
 *
 * @property integer $id
 * @property string $title
 * @property string $sub_title
 * @property string $toggle_url
 * @property integer $sort_order
 * @property string $zhifa_logo
 */
class IndexZhifa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_zhifa';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order'], 'integer'],
            [['title'], 'string', 'max' => 20],
            [['sub_title'], 'string', 'max' => 64],
            [['toggle_url',], 'string', 'max' => 50],
            [['zhifa_logo'],
             'image',
             'extensions' => 'jpg, jpeg, gif, png',
             'on' => ['insert', 'update']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '主标题',
            'sub_title' => '副标题',
            'toggle_url' => '跳转链接',
            'sort_order' => '排序',
            'zhifa_logo' => '图片',
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
                'attribute' => 'zhifa_logo',//上传的属性
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/zhifa/{id}',//保存的路径 可用Model->getUploadPath
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/zhifa/{id}',//生成的url 可调用Model->getUploadUrl
                'thumbs' => [],
            ],
        ];
    }
}
