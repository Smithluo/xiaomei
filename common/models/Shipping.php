<?php

namespace common\models;

use common\helper\TextHelper;
use Yii;

/**
 * This is the model class for table "o_shipping".
 *
 * @property integer $shipping_id
 * @property string $shipping_code
 * @property string $shipping_name
 * @property string $shipping_desc
 * @property string $insure
 * @property integer $support_cod
 * @property integer $enabled
 * @property string $shipping_print
 * @property string $print_bg
 * @property string $config_lable
 * @property integer $print_model
 * @property integer $shipping_order
 */
class Shipping extends \yii\db\ActiveRecord
{
    const ENABLED_NO    = 0;
    const ENABLED_YES   = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_shipping';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['support_cod', 'enabled', 'print_model', 'shipping_order'], 'integer'],
            [['shipping_print'], 'required'],
            [['shipping_print', 'config_lable'], 'string'],
            [['shipping_code'], 'string', 'max' => 20],
            [['shipping_name'], 'string', 'max' => 120],
            [['shipping_desc', 'print_bg'], 'string', 'max' => 255],
            [['insure'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shipping_id' => 'Shipping ID',
            'shipping_code' => 'Shipping Code',
            'shipping_name' => 'Shipping Name',
            'shipping_desc' => 'Shipping Desc',
            'insure' => 'Insure',
            'support_cod' => 'Support Cod',
            'enabled' => 'Enabled',
            'shipping_print' => 'Shipping Print',
            'print_bg' => 'Print Bg',
            'config_lable' => 'Config Lable',
            'print_model' => 'Print Model',
            'shipping_order' => 'Shipping Order',
        ];
    }

    /**
     * 获取商品的默认配送方式
     *
     * 默认取商品的配送方式，如果商品没有设配送方式，则取品牌的配送方式，如果还没有则取默认
     * @return int|mixed
     */
    public static function getDefaultShippingId()
    {
        $defaultCode = Yii::$app->params['default_shipping_code'];
        $rs = self::find()->select('shipping_id')->where(['shipping_code' => $defaultCode])->one();

        if ($rs) {
            return $rs->shipping_id;
        } else {
            return 0;
        }
    }

    /**
     * 根据配送方式code获取配送方式id,如果获取不到，返回默认配送方式的id
     *
     * @param $shippingCode free | fpd | fpbs
     * @return integer
     */
    public static function getShippingIdByCode($shippingCode)
    {
        $rs = self::find()->select('shipping_id')->where(['shipping_code' => $shippingCode])->one();

        if ($rs) {
            return $rs->shipping_id;
        } else {
            $shippingCode = Yii::$app->params['default_shipping_code'];
            $rs = self::find()->select('shipping_id')->where(['shipping_code' => $shippingCode])->one();
            return $rs->shipping_id;
        }
    }

    /**
     * 根据配送方式code获取配送方式id,如果获取不到，返回默认配送方式的id
     * @param $shippingId
     * @return mixed
     */
    public static function getShippingCodeById($shippingId)
    {
        $rs = self::find()->select('shipping_code')->where(['shipping_code' => $shippingId])->one();

        if ($rs) {
            return $rs->shipping_code;
        } else {
            return Yii::$app->params['default_shipping_code'];
        }
    }

    /**
     * 关联配送区域
     * @return \yii\db\ActiveQuery
     */
    public function getShippingArea()
    {
        return $this->hasMany(ShippingArea::className(), ['shipping_id' => 'shipping_id']);
    }

    /**
     * 取得某配送方式对应于某收货地址的区域信息
     * @param   int     $shippingId     配送方式id
     * @param   array   $regionIdList   收货人地区id数组
     * @return  array   配送区域信息（config 对应着反序列化的 configure）
     *
     * @return array|null|\yii\db\ActiveRecord|\yii\db\Command
     */
    public static function shippingAreaInfo($shippingId, $regionIdList)
    {
        $rs = [];
        $shipping = self::find()
            ->joinWith([
                'shippingArea',
                'shippingArea.areaRegion'
            ])->where([
                self::tableName().'.shipping_id' => $shippingId,
                AreaRegion::tableName().'.region_id ' => $regionIdList,
            ])->asArray()
            ->one();

        $configure = [];
        if (!empty($shipping)) {
            if (!empty($shipping['shippingArea'])) {
                foreach ($shipping['shippingArea'] as $item) {
                    if (!empty($configure)) {
                        break;
                    } elseif (!empty($item['areaRegion'])) {
                        $regionIdMap = array_column($item['areaRegion'], 'region_id');
                        foreach ($regionIdList as $regionId) {
                            if (in_array($regionId, $regionIdMap)) {
                                $rs['shipping_area_name'] = $item['shipping_area_name'];
                                $rs['shipping_code'] = $shipping['shipping_code'];
                                $rs['shipping_name'] = $shipping['shipping_name'];
                                $rs['shipping_desc'] = $shipping['shipping_desc'];
                                $rs['shipping_id'] = $shipping['shipping_id'];
                                $rs['shipping_config'] = TextHelper::unserializeConfig($item['configure']);
                                break 2;    //  跳出两层循环
                            }
                        }

                    }
                }
            }
//            $shipping_config = TextHelper::unserializeConfig($configure);
        }

        return $rs;
    }
}
