<?php

namespace common\models;

use common\helper\TextHelper;
use Faker\Provider\Text;
use Yii;

/**
 * 配送方式下的 不同区域的配送规则
 * This is the model class for table "o_shipping_area".
 *
 * @property integer $shipping_area_id
 * @property string $shipping_area_name
 * @property integer $shipping_id
 * @property string $configure
 */
class ShippingArea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_shipping_area';
    }

    /**
     * 处理序列化的支付、配送的配置参数
     * 返回一个以name为索引的数组
     *
     * @access  public
     * @param   string       $cfg
     * @return  void
     */
    public static function unserializeConfig($cfg) {
        if (is_string($cfg) && ($arr = unserialize($cfg)) !== false) {
            $config = array();

            foreach ($arr AS $key => $val) {
                $config[$val['name']] = $val['value'];
            }

            return $config;
        } else {
            return false;
        }
    }

    public function getConfig() {
        return self::unserializeConfig($this->configure);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shipping_id'], 'integer'],
            [['configure'], 'required'],
            [['configure'], 'string'],
            [['shipping_area_name'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shipping_area_id' => '配送区域ID',
            'shipping_area_name' => '区域运费规则',
            'shipping_id' => '配送方式ID',
            'configure' => '区域配置',
        ];
    }

    public function getAreaConfigure() {
        return unserialize($this->configure);
    }

    /**
     * 关联配送方式，多对一
     * @return \yii\db\ActiveQuery
     */
    public function getShipping()
    {
        return $this->hasOne(Shipping::className(), ['shipping_id' => 'shipping_id']);
    }

    /**
     * 关联配送区域
     * @return \yii\db\ActiveQuery
     */
    public function getAreaRegion()
    {
        return $this->hasMany(AreaRegion::className(), ['shipping_area_id' => 'shipping_area_id']);
    }

    /**
     * 取得某配送方式对应于某收货地址的区域信息
     * @param   int     $shippingId     配送方式id
     * @param   array   $regionIdList   收货人地区id数组
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getShippingInfo($shippingId, $regionIdList)
    {
        $shippingAreaInfo = ShippingArea::find()
            ->joinWith([
                'shipping',
                'areaRegion areaRegion'
            ])->where([
                self::tableName().'.shipping_id' => $shippingId,
            ])->andWhere([
                'areaRegion.region_id' => $regionIdList,
            ])->one();

        //  如果 邮费政策是包邮，但收获地址的区域在偏远地区，修正邮费政策为到付
        if ($shippingId == 2 && $shippingAreaInfo->shipping_area_name == '到付' && !empty($shippingAreaInfo->configure)) {
            $shippingConfig = TextHelper::unserializeConfig($shippingAreaInfo->configure);
            $shippingCode = $shippingConfig['backup_shipping_code'];
            $shippingId = Shipping::getShippingIdByCode($shippingCode);

            $shippingAreaInfo = self::getShippingInfo($shippingId, $regionIdList);
        }

        return $shippingAreaInfo;
    }

    /**
     * 通过用户地址和配送方式获取 修正后的配送方式
     * @param array $addressArr   用户地址的 国家、省、市、县区
     * @param $shippingCode         配送方式
     * @return mixed
     */
    public static function modifyShippingDesc($addressArr, $shippingCode)
    {
        $shippingId = Shipping::getShippingIdByCode($shippingCode);
        $shippingAreaInfo = self::getShippingInfo($shippingId, $addressArr);

        //  没有匹配到区域 则使用配送政策的名称，有匹配到区域，则使用区域名称
        if (empty($shippingAreaInfo->shipping_area_name)) {
            $shippingDesc = $shippingAreaInfo->shipping->shipping_name;
        } else {
            $shippingDesc = $shippingAreaInfo->shipping_area_name;
        }

        return $shippingDesc;
    }
}
