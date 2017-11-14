<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "o_resource_site".
 *
 * @property integer $id
 * @property string $site_name
 * @property string $site_logo
 */
class ResourceSite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_resource_site';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_name'], 'required'],
            ['site_logo', 'required', 'on' => 'insert'],
            [
                'site_logo',
                'image',
                'extensions' => 'jpg, jpeg, gif, png',
                'on' => ['insert', 'update']
            ],
            [['site_name'], 'string', 'max' => 40],
//            [['site_logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_name' => '站点名称',
            'site_logo' => '站点logo',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => \mongosoft\file\UploadImageBehavior::className(),
                'attribute' => 'site_logo',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/resource_site_logo/',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/resource_site_logo',
                'thumbs' => [],
            ],
        ];
    }

    /**
     * 获取来源站点 的映射关系
     * @return array
     */
    public  static function getResourceSiteMap()
    {
        $resourceSiteList = self::find()->select(['id', 'site_name'])->indexBy('id')->all();
        return ArrayHelper::getColumn($resourceSiteList, 'site_name');
    }
}
