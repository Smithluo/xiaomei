<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "o_region".
 *
 * @property integer $region_id
 * @property integer $city_code
 * @property string $region_name
 * @property integer $parent_id
 * @property integer $agency_id
 * @property integer $region_type
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_code', 'region_name', 'parent_id'], 'required'],
            [['city_code', 'parent_id', 'agency_id', 'region_type'], 'integer'],
            [['region_name'], 'string', 'max' => 63],
            ['region_type', 'default', 'value' => 2],
            ['agency_id', 'default', 'value' => 0],
            ['parent_id', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'region_id' => 'ID',
            'city_code' => '区域编码',
            'region_name' => '区域名称',
            'parent_id' => '父级ID',
            'agency_id' => 'Agency ID',
            'region_type' => '区域类型',    //  ECTouch交付时缺少的字段
        ];
    }

    /**
     * @inheritdoc
     * @return RegionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RegionQuery(get_called_class());
    }

    /**
     * 获取单个 region_name
     * @param $region_id
     * @return string
     */
    public static function getRegionName($region_id)
    {
        $rs = self::find('region_name')->where(['region_id' => $region_id])->one();
        if ($rs && $rs->region_name) {
            return $rs->region_name;
        } else {
            return '';
        }

    }

    /**
     * 获取多个region_name
     * @param $region_id_array
     * @return array|string
     */
    public static function getRegionNames($region_id_array)
    {
        $rs = self::find('region_name', 'region_id')->where(['region_id' => $region_id_array])->asArray()->all();
        if ($rs) {
            return array_column($rs, 'region_name', 'region_id');
        } else {
            return '';
        }
    }

    /**
     * 获取地址
     * @param $regionIdList
     * @param $address
     * @return string
     */
    public static function getAddress($regionIdList, $address) {
        $reqList = [
            $regionIdList['province'],
            $regionIdList['city'],
        ];

        if (!empty($regionIdList['district'])) {
            $reqList[] = $regionIdList['district'];
        }

        $regionNames = self::getRegionNames($reqList);

        $province = (empty($regionIdList['province']) || empty($regionNames[$regionIdList['province']])) ? '' : $regionNames[$regionIdList['province']];
        $city = (empty($regionIdList['city']) || empty($regionNames[$regionIdList['city']])) ? '' : $regionNames[$regionIdList['city']];
        $district = (empty($regionIdList['district']) || empty($regionNames[$regionIdList['district']])) ? '' : $regionNames[$regionIdList['district']];

        return ''. $province. ' '. $city. ' '. $district. ' '. $address;
    }

    /**
     * 获取用户收件地址的 省市县区
     * @param $model
     * @return bool|string
     */
    public static function getUserAddress($model)
    {
        $address_region_array = [];
        if ($model->province) {
            $address_region_array[] = $model->province;
        }
        if ($model->city) {
            $address_region_array[] = $model->city;
        }
        if ($model->district) {
            $address_region_array[] = $model->district;
        }

        if ($address_region_array) {
            $rs = self::find('region_name', 'region_id')
                ->where(['region_id' => $address_region_array])
                ->asArray()
                ->all();
            $region_names = array_column($rs, 'region_name', 'region_id');
            return trim(implode(' ', $region_names));
        } else {
            return '';
        }

    }

    /**
     * 获取省级行政单位
     */
    public static function getProvinceMap()
    {
        $rs = self::find('region_id', 'region_name')
            ->where(['parent_id' => 1])
            ->asArray()
            ->all();

        return array_column($rs, 'region_name', 'region_id');
    }

    /**
     * 获取省级行政单位对应的市级行政单位
     */
    public static function getCityMap($province_id)
    {
        $rs = self::find('region_id', 'region_name')
            ->where(['parent_id' => $province_id])
            ->asArray()
            ->all();

        return array_column($rs, 'region_name', 'region_id');
    }

    public function getChildren() {
        return $this->hasMany(Region::className(), ['parent_id' => 'region_id']);
    }

    /**
     * 获取国家 的 region_id => region_name 映射关系
     * @return array
     */
    public static function getCountryMap()
    {
        $countryMap = [];
        $countryList = self::find()
            ->select(['region_id', 'region_name'])
            ->where(['parent_id' => 0])
            ->indexBy('region_id')
            ->all();

        if (!empty($countryList)) {
            $countryMap = ArrayHelper::getColumn($countryList, 'region_name', true);
        }

        return $countryMap;
    }
}
