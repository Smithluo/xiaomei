<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_touch_brand".
 *
 * @property integer $brand_id
 * @property string $brand_banner
 * @property string $brand_content
 * @property string $brand_qualification
 * @property string $license
 */
class TouchBrand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_touch_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id'], 'required'],
            [['brand_content', 'brand_qualification', 'license'], 'safe'],
            [['brand_banner'], 'required', 'on' => 'insert'],
            [['brand_content', 'brand_qualification', 'license'], 'default', 'value' => ''],
            [['brand_id'], 'integer'],
            [['brand_content', 'brand_qualification', 'license'], 'string'],

            [
                ['brand_banner'],
                'image',
                'checkExtensionByMimeType' => false,
                'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'brand_id' => 'Brand ID',
            'brand_banner' => '品牌banner图',
            'brand_content' => '详情',
            'brand_qualification' => '品牌资质',
            'license' => '品牌授权',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\behaviors\UploadImageBehavior::className(),
                'attribute' => 'brand_banner',
                'scenarios' => ['insert', 'update'],
                'path' => '@mRoot/data/attached/banner_image/{brand_id}',
                'storePrefix' => 'data/attached/banner_image/{brand_id}',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/banner_image/{brand_id}',
                'thumbs' => [],
            ],
        ];
    }
}
