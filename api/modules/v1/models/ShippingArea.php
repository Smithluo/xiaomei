<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27 0027
 * Time: 21:14
 */

namespace api\modules\v1\models;

class ShippingArea extends \common\models\ShippingArea
{
//    public $areaRegion;

    public function fields()
    {
        return [
            'shipping_area_id' => function($model){
                return (int)$model->shipping_area_id;
            },  //  '配送区域ID',
            'shipping_area_name' => function($model){
                return (string)$model->shipping_area_name;
            },  //  '配送区域名称',
            'shipping_id' => function($model){
                return (int)$model->shipping_id;
            },  //  '配送方式ID',
            'configure' => function($model){
                return (string)$model->configure;
            },  //  '区域配置',

            'shipping',
            'areaRegion',
        ];
    }

    public function getRegions() {
        return $this->hasMany(Region::className(), [
            'region_id' => 'region_id',
        ])->viaTable(AreaRegion::tableName(), [
            'shipping_area_id' => 'shipping_area_id',
        ]);
    }

    /**
     * 取得某配送方式对应于某收货地址的区域信息
     *
     * @param $shipping_id  $shipping_id        配送方式id
     * @param $regionIdList $region_id_list     收货人地区id数组
     * @return array        配送区域信息（config 对应着反序列化的 configure）
     */
    public static function shippingRreaInfo($shipping_id, $regionIdList) {
        $sTb = Shipping::tableName();

        $rs = self::find()
            ->joinWith('shipping')
            ->joinWith(['areaRegion' => function($query) {
                $query->asArray();
            }])->where([
                $sTb.'.shipping_id' => $shipping_id,
                $sTb.'.enabled' => 1,
            ])->all();


        $row = [];
        if ($rs) {
            foreach ($rs as $item) {
                $shipping_config = self::unserializeConfig($item->configure);
                $row['shipping_config']  = [];

                //  如果用户当前没有地址，则不判定是否需要改变配送方式
                if (!empty($regionIdList['province'])) {
                    if (!empty($item['areaRegion'])) {
                        $shippingAreaRegionIdList = array_column($item['areaRegion'], 'region_id');

                        //  如果 用户收件地址的省份 在当前配送模板的地址列表中  ——当前只配置到省份，如果要细化到城市一级的话，所有的配置都细化到城市一级，用城市ID判断
                        if (in_array($regionIdList['province'], $shippingAreaRegionIdList)) {
                            $row['shipping_config'] = $shipping_config;
                        }
                    }

                    //  如果有多条记录（一种配送方式分区域配置了），
                    //      第一次没有匹配到区域，则跳出本次循环; 如果匹配到了，则不再继续循环，在本次循环结束的地方跳过
                    if (count($rs) > 1) {
                        if (empty($row['shipping_config'])) {
                            continue;
                        } else {
                            $break = true;
                        }
                    }
                }

                if (isset($shipping_config['pay_fee'])) {
                    if (strpos($shipping_config['pay_fee'], '%') !== false) {
                        $row['pay_fee'] = floatval($shipping_config['pay_fee']) . '%';
                    } else {
                        $row['pay_fee'] = floatval($shipping_config['pay_fee']);
                    }
                } else {
                    $row['pay_fee'] = 0.00;
                }

                if (!empty($item['shipping']['shipping_id'])) {
                    $row['shipping_id'] = $item['shipping']['shipping_id'];
                    $row['shipping_code'] = $item['shipping']['shipping_code'];
                    $row['shipping_name'] = $item['shipping']['shipping_name'];
                    $row['shipping_desc'] = $item['shipping']['shipping_name'];
                }

                if (isset($break) && $break) {
                    break;
                }
            }
        }

        return $row;
    }
}