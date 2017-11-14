<?php

namespace service\models;

use common\models\ServicerSpecStrategy;
use common\models\ServicerStrategy;
use Yii;

/**
 * This is the model class for table "o_brand".
 *
 * @property integer $brand_id
 * @property string $brand_name
 * @property string $brand_depot_area
 * @property string $brand_logo
 * @property string $brand_logo_two
 * @property string $brand_bgcolor
 * @property string $brand_policy
 * @property string $brand_desc
 * @property string $brand_desc_long
 * @property string $short_brand_desc
 * @property string $site_url
 * @property integer $sort_order
 * @property integer $is_show
 * @property integer $album_id
 * @property string $brand_tag
 * @property string $servicer_strategy_id
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_depot_area', 'brand_logo_two', 'brand_desc', 'short_brand_desc', 'brand_tag'], 'required'],
            [['brand_desc', 'brand_desc_long'], 'string'],
            [['sort_order', 'is_show', 'album_id', 'servicer_strategy_id'], 'integer'],
            [['brand_name', 'brand_depot_area'], 'string', 'max' => 60],
            [['brand_logo'], 'string', 'max' => 80],
            [['brand_logo_two', 'brand_policy', 'site_url'], 'string', 'max' => 255],
            [['brand_bgcolor'], 'string', 'max' => 10],
            [['short_brand_desc'], 'string', 'max' => 100],
            [['brand_tag'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'brand_id' => 'Brand ID',
            'brand_name' => 'Brand Name',
            'brand_depot_area' => '品牌发货地址',
            'brand_logo' => 'Brand Logo',
            'brand_logo_two' => '品牌详情页小logo 220*80',
            'brand_bgcolor' => '品牌logo的背景色,显示字品牌详情页面',
            'brand_policy' => '品牌政策，当前用图片，后面增加品牌政策表，做品牌促销方案和全站促销方案',
            'brand_desc' => 'Brand Desc',
            'brand_desc_long' => '品牌列表页左侧显示的文案',
            'short_brand_desc' => 'Short Brand Desc',
            'site_url' => 'Site Url',
            'sort_order' => 'Sort Order',
            'is_show' => 'Is Show',
            'album_id' => 'Album ID',
            'brand_tag' => 'Brand Tag',
            'servicer_strategy_id' => 'Servicer Strategy ID',
        ];
    }

    /**
     * @inheritdoc
     * @return BrandQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BrandQuery(get_called_class());
    }

    public function getServicerStrategy() {
        return $this->hasOne(ServicerStrategy::className(), ['id' => 'servicer_strategy_id']);
    }

    public function getSpecServicerStrategy() {
        return ServicerSpecStrategy::find()->where(['brand_id' => $this->brand_id, 'servicer_user_id'=>Yii::$app->user->identity['user_id']])->orderBy(['id' => SORT_DESC])->one();
//        return $this->hasOne(ServicerSpecStrategy::className(), ['brand_id' => 'brand_id', ''])->where(['servicer_user_id' => Yii::$app->user->identity['user_id']]);
    }
}
