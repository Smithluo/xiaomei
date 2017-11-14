<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_activity".
 *
 * @property integer $id
 * @property string $title
 * @property string $sub_title
 * @property string $m_url
 * @property integer $sort_order
 * @property string $index_logo
 */
class IndexCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order'], 'integer'],
            [['title'], 'string', 'max' => 20],
            [['m_url'], 'string', 'max' => 50],
            [['logo'], 'image', 'extensions' => 'jpg, jpeg, gif, png','on' => ['insert', 'update']],
            [['title', 'sort_order', 'm_url'], 'required'],
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
            'm_url' => '链接',
            'sort_order' => '排序',
            'logo' => 'Logo',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => \mongosoft\file\UploadImageBehavior::className(),
                'attribute' => 'logo',//上传的属性
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/category/{id}',//保存的路径 可用Model->getUploadPath
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/category/{id}',//生成的url 可调用Model->getUploadUrl
                'thumbs' => [],
            ],
        ];
    }
}
