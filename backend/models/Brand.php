<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/1
 * Time: 10:53
 */

namespace backend\models;

use backend\models\Shipping;

class Brand extends \common\models\Brand
{

    public $brandCatIds = [];
    //品牌资料的区域 index用到下拉筛选
    public static $brand_area_map = [
        '韩国品牌' => '韩国品牌',
        '美澳品牌' => '美澳品牌',
        '港台品牌' => '港台品牌',
        '欧洲品牌' => '欧洲品牌',
        '日本品牌' => '日本品牌',
        '其它品牌' => '其它品牌',
    ];

    /**
     * 获取当前还没有绑定品牌商的品牌ID
     * @return array
     */
    public static function getUnBindBrandMap()
    {
        $rs = self::find()->select('brand_id, brand_name')
            ->where([
                'supplier_user_id' => 0,
                'is_show' => self::IS_SHOW,
            ])->asArray()
            ->all();
        if ($rs) {
            return array_column($rs, 'brand_name', 'brand_id');
        } else {
            return [];
        }
    }

    /**
     * 获取当前用户已经绑定品牌商的品牌ID
     * @return array
     */
    public static function getBindBrandMap($user_id)
    {
        $rs = self::find()->select('brand_id, brand_name')
            ->where(['supplier_user_id' => $user_id])
            ->asArray()
            ->all();
        if ($rs) {
            return array_column($rs, 'brand_name', 'brand_id');
        } else {
            return [];
        }
    }

    /**
     * 获取需要显示给当前用户的品牌列表
     * @return array
     */
    public static function getBrandMaShow($user_id)
    {
        $rs = self::find()->select('brand_id, brand_name')
            ->where([
                'supplier_user_id' => 0,
                'is_show' => self::IS_SHOW,
            ])->orWhere([
                'supplier_user_id' => $user_id
            ])->asArray()
            ->all();
        if ($rs) {
            return array_column($rs, 'brand_name', 'brand_id');
        } else {
            return [];
        }
    }

    public static function setSupplier($brand_id_list, $supplier_user_id)
    {
        //  释放取消掉的品牌关联
        $brand_free = self::find()
            ->where(['not in', 'brand_id', $brand_id_list])
            ->andWhere(['supplier_user_id' => $supplier_user_id])
            ->all();
        if ($brand_free) {
            foreach ($brand_free as $brand) {
                $brand->supplier_user_id = 0;
                $brand->save();
            }
        }

        $brand_list = self::find()->where([
            'brand_id' => $brand_id_list
        ])->all();

        if ($brand_list) {
            foreach ($brand_list as $brand) {
                $brand->supplier_user_id = $supplier_user_id;
                $brand->save();
            }
            return [
                'code' => 0,
                'msg' => '设置成功',
            ];
        } else {
            return [
                'code' => 1,
                'msg' => '您选择的品牌无效',    //  非法操作
            ];
        }
    }

    /**
     * 获取供应商
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierUser() {
        return $this->hasOne(Users::className(), ['user_id' => 'supplier_user_id']);
    }

    /**
     * 获取配送方式
     * @return \yii\db\ActiveQuery
     */
    public function getShipping() {
        return $this->hasOne(Shipping::className(), ['shipping_id' => 'shipping_id']);
    }

    /**
     * 获取品牌的 brand_id => brand_name 映射
     * @return array
     */
    public static function getBrandIdNameMap()
    {
        $brandMap = [];
        $brandList= Brand::find()->select(['brand_id', 'brand_name', 'is_show'])->all();
        foreach ($brandList as $brand) {
            if ($brand->is_show == Brand::IS_NOT_SHOW) {
                $brandMap[$brand->brand_id] = '[已下架]['.$brand->brand_id.']'.$brand->brand_name;
            } else {
                $brandMap[$brand->brand_id] = '['.$brand->brand_id.']'.$brand->brand_name;
            }

        }
        return $brandMap;
    }

    /**
     * 获取商品 brand_id => [is_show] brand_name [country] brand_desc 映射
     * @return array
     */
    public static function getBrandListWithStatusMap()
    {
        $brandMap = [];

        $rs = self::find()
            ->select(['brand_id', 'brand_name', 'is_show', 'country', 'brand_desc'])
            ->all();


        if (!empty($rs)) {
            foreach ($rs as $brand) {
                $brandName = '';

                if ($brand->is_show == self::IS_NOT_SHOW) {
                    $brandName .= '[-已下架-]';
                }
                $brandName .= $brand->brand_id.' '.$brand->brand_name;
                if (empty($brand->country)) {
                    $brandName .= '[-国家未填写-]';
                } else {
                    $brandName .= '['.$brand->country.']';
                }
                if (empty($brand->brand_desc)) {
                    $brandName .= '[-品牌描述未填写-]';
                } else {
                    $brandName .= $brand->brand_desc;
                }

                $brandMap[$brand->brand_id] = $brandName;
            }
        }
        return $brandMap;
    }

//    public function getBrandCat()
//    {
//        return parent::getBrandCat(); // TODO: Change the autogenerated stub
//    }
}