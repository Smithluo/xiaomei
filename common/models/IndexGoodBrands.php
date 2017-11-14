<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_good_brands".
 *
 * @property integer $id
 * @property string $brand_id
 * @property string $title
 * @property integer $sort_order
 * @property integer $index_logo
 */
class IndexGoodBrands extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_good_brands';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'sort_order'], 'integer'],
            [['index_logo'],
             'image',
             'extensions' => 'jpg, jpeg, gif, png',
             'on' => ['insert', 'update']
            ],
            ['title', 'string' , 'max' => 20 ],
            [['brand_id', 'sort_order', 'title'], 'required'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand_id' => '品牌',
            'title' => '标题',
            'sort_order' => '排序',
            'index_logo' => 'logo'
        ];
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::className(), [ 'brand_id' => 'brand_id']);
    }

    public static function Brands()
    {
        $brandList = Brand::find()->where([
            'is_show' =>  1
        ])
            ->orderBy('sort_order')
            ->asArray()
            ->all();

        return array_column($brandList, 'brand_name' , 'brand_id');
    }

    public function behaviors()
    {
        return [
            [
                'class' => \mongosoft\file\UploadImageBehavior::className(),
                'attribute' => 'index_logo',//上传的属性
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/brands/{id}',//保存的路径 可用Model->getUploadPath
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/brands/{id}',//生成的url 可调用Model->getUploadUrl
                'thumbs' => [],
            ],
        ];
    }
}
