<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "o_user_address".
 *
 * @property string $address_id
 * @property string $address_name
 * @property string $user_id
 * @property string $consignee
 * @property string $company_name
 * @property string $email
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property string $address
 * @property string $zipcode
 * @property string $tel
 * @property string $mobile
 * @property string $sign_building
 * @property string $best_time
 * @property integer $is_default
 */
class UserAddress extends \yii\db\ActiveRecord
{
    const IS_DEFAULT = 1;
    const IS_NOT_DEFAULT = 0;

    const CHECK_NOT_EXIST = 0;
    const CHECK_NOT_VALID = 1;
    const CHECK_VALID = 2;

    public static $defaultMap = [
        self::IS_DEFAULT => '默认',
        self::IS_NOT_DEFAULT => '非默认'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_user_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'province', 'city', 'mobile'], 'required'],
            [['user_id', 'country', 'province', 'city', 'district', 'is_default'], 'integer'],
            [['address_name'], 'string', 'max' => 50],
            [['consignee', 'email', 'zipcode', 'tel', 'mobile'], 'string', 'max' => 60],
            [['company_name'], 'string', 'max' => 30],
            [['country'], 'default', 'value' => 1],
            [['address', 'sign_building', 'best_time'], 'string', 'max' => 120],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'address_id' => 'Address ID',
            'address_name' => 'Address Name',
            'user_id' => 'User ID',
            'consignee' => 'Consignee',
            'company_name' => '门店名称',
            'email' => 'Email',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'address' => 'Address',
            'zipcode' => 'Zipcode',
            'tel' => 'Tel',
            'mobile' => 'Mobile',
            'sign_building' => 'Sign Building',
            'best_time' => 'Best Time',
            'is_default' => '是否是默认地址。1:是; 0:否;',
        ];
    }

    public static function getCompleteAddress($address_id ) {
        $address = '';
        if ($address_id) {
            $address_model = self::findOne($address_id);
            if ($address_model) {
                $address_arr = Region::getRegionNames([
                    $address_model->province,
                    $address_model->city,
                    $address_model->district,
                ]);

                if ($address_arr) {
                    $address = implode(' ', $address_arr);
                }
                if ($address_model->address) {
                    $address .= ' '.$address_model->address;
                }
                if ($address_model->consignee) {
                    $address .= ' '.$address_model->consignee;
                }
                if ($address_model->mobile) {
                    $address .= ' '.$address_model->mobile;
                }
            }
        }
        return trim($address);
    }

    /**
     * 根据用户id获取地址列表
     * @param $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getList($userId)
    {
        return self::find()
            ->where(['user_id' => $userId])
            ->orderBy([
                'address_id' => SORT_DESC
            ])->all();
    }

    /**
     * 获取收获地址
     * @param $userId
     * @param $addressId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getAddressBuyId($userId, $addressId)
    {
        return self::find()
            ->where([
                'user_id' => $userId,
                'address_id' => $addressId,
            ])->one();
    }

    /**
     * 获取用户最后一次添加的收获地址
     * @param int $userId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getLastAddress($userId)
    {
        return self::find()
            ->where(['user_id' => $userId])
            ->orderBy([
                'address_id' => SORT_DESC
            ])->one();
    }

    /**
     * 检查 指定的收获地址信息是否有效并完整
     *
     * @return int 0:地址不存在; 1:有地址但信息不完整; 2:有地址并且信息完整
     */
    public function check()
    {
        if ($this) {
            if (
                $this->consignee &&
                $this->province &&
                $this->city &&
                $this->address &&
                $this->mobile
            ) {
                return self::CHECK_VALID;
            } else {
                return self::CHECK_NOT_VALID;
            }
        } else {
            return self::CHECK_NOT_EXIST;
        }
    }

    /**
     * 关联地址的的省份信息
     * @return \yii\db\ActiveQuery
     */
    public function getProvinceName()
    {
        return $this->hasOne(Region::className(), ['region_id' => 'province']);
    }

    /**
     * 关联地址的的城市信息
     * @return \yii\db\ActiveQuery
     */
    public function getCityName()
    {
        return $this->hasOne(Region::className(), ['region_id' => 'city']);
    }

    /**
     * 关联地址的的县区信息
     * @return \yii\db\ActiveQuery
     */
    public function getDistrictName()
    {
        return $this->hasOne(Region::className(), ['region_id' => 'district']);
    }

    /**
     * 获取收货人信息列表 并排序：选中的地址\默认地址，其他地址
     * @param $userId
     * @param $addressId    选中状态的地址
     * @param $defaultId    默认地址
     * @return array
     */
    public static function orderedConsigneeList($userId, $addressId, $defaultId)
    {
        $consignee_list = UserAddress::getList($userId);
        $orderedConsigneeList = [];
        $selectedConsigneeInfo = [];    //  选中的地址
        $defulatConsigneeInfo = [];     //  默认地址    可能也是选中的，要避免重复显示
        foreach ($consignee_list as $consignee_info) {

            //  获取地址的 省、市、县区名称
            $province_name = $consignee_info->provinceName->region_name ?: '';
            $city_name = $consignee_info->cityName->region_name ?: '';
            $district_name = $consignee_info->districtName->region_name ?: '';
            $consignee_info = ArrayHelper::toArray($consignee_info);
            $consignee_info['province_name'] = $province_name;
            $consignee_info['city_name'] = $city_name;
            $consignee_info['district_name'] = $district_name;

            //  分离出默认地址和当前选中的地址（选中默认地址不跳页）
            if ($consignee_info['address_id'] == $addressId) {
                $selectedConsigneeInfo = $consignee_info;
            } elseif ($consignee_info['address_id'] == $defaultId) {
                $defulatConsigneeInfo = $consignee_info;
            } else {
                $orderedConsigneeList[] = $consignee_info;
            }

        }
        //  如果选中的不是默认地址，则先插入默认地址，再插入选中地址到数组头部，确保最先显示选中地址、其次默认地址
        if ($addressId != $defaultId && !empty($defulatConsigneeInfo)) {
            array_unshift($orderedConsigneeList, $defulatConsigneeInfo);
        }
        if (!empty($selectedConsigneeInfo)) {
            array_unshift($orderedConsigneeList, $selectedConsigneeInfo);
        }

        return $orderedConsigneeList;
    }
}
