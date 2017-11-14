<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/21
 * Time: 17:07
 */

namespace api\modules\v1\models;

use yii\helpers\ArrayHelper;

class UserAddress extends \common\models\UserAddress
{
    /**
     * 分场景指定数据校验规则
     *
     * is_default   废弃 默认为0  不再做修改，用户的默认地址放在o_users表中
     *
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['user_id', 'province', 'city', 'mobile', 'consignee'], 'required'],
            [['user_id', 'country', 'province', 'city', 'district', 'is_default'], 'integer'],
//            ['mobile', 'match', 'pattern' => '/^[1][0-9]{10}$/', 'message' => '手机号格式不正确'],
            ['mobile', 'mobile'],
            ['country', 'default', 'value' => 1],
            ['email', 'email'],
            ['is_default', 'in', 'range' => [0, 1]],
            ['is_default', 'default', 'value' => 0],
            ['district', 'default', 'value' => 0],
            [['address_name'], 'string', 'max' => 50],
//            [['company_name'], 'string', 'max' => 30],
            [['address', ], 'string', 'max' => 120],    //  'sign_building', 'best_time'
            [['consignee', 'email', 'zipcode', 'tel', 'mobile'], 'string', 'max' => 60],

            ['district', 'required', 'on' => 'update']
        ]);
    }

    /**
     * 校验手机号
     * @param $attr
     * @param $params
     */
    public function mobile($attr, $params)
    {
        $mobile = $this->mobile;
        if (!is_numeric($mobile)) {
            $this->addError($attr, '手机号是纯数字');
        } elseif ($mobile < 10000000000 || $mobile > 19999999999) {
            $this->addError($attr, '请输入正确手机号');
        }
    }

    /**
     * 格式化输出数据
     */
    public function fields()
    {
        return [
            'address_id' => function ($model) {
                return (int)$model->address_id;
            },
            'user_id' => function ($model) {
                return (int)$model->user_id;
            },
            'country' => function ($model) {
                return (int)$model->country;
            },
            'province' => function ($model) {
                return (int)$model->province;
            },
            'city' => function ($model) {
                return (int)$model->city;
            },
            'district' => function ($model) {
                return (int)$model->district;
            },

            'is_default' => function ($model) {
                return (int)$model->is_default;
            },
            'zipcode' => function ($model) {
                return (int)$model->zipcode;
            },
            'address_name' => function ($model) {
                return (string)$model->address_name;
            },
            'mobile' => function ($model) {
                return (string)$model->mobile;
            },
            'email' => function ($model) {
                return (string)$model->email;
            },
            'consignee' => function ($model) {
                return (string)$model->consignee;
            },
            'address' => function ($model) {
                return (string)$model->address;
            },
            'tel' => function ($model) {
                return (string)$model->tel;
            },
            /*'sign_building' => function ($model) {
                return (string)$model->sign_building;
            },
            'best_time' => function ($model) {
                return (string)$model->best_time;
            },
            'company_name' => function ($model) {
                return (string)$model->company_name;
            },*/
        ];
    }

    /**
     * 根据用户id获取地址列表
     * @param $user_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getList($user_id)
    {
        return self::find()->where(['user_id' => $user_id])
            ->orderBy([
                'address_id' => SORT_DESC
            ])
            ->all();
    }

    /**
     * 检查是否有 有效的 默认收货地址
     *
     * @param $user_id
     * @param $address_id
     * @return bool
     */
    public static function checkDeafultAddress($user_id, $address_id)
    {
        if (empty($address_id)) {
            $hasDefault = false;
        } else {
            $defaultAddress = self::find()->where([
                'address_id' => $address_id,
                'user_id' => $user_id,
            ])->one();

            if ($defaultAddress) {
                $hasDefault = true;
            } else {
                $hasDefault = false;
            }
        }

        return $hasDefault;
    }

    /**
     * 检查 指定的收获地址信息是否有效并完整
     *
     * return int 0:没有地址; 1:有地址但信息不完整; 2:有地址并且信息完整
     */
    public static function checkUserAddress($user_id, $address_id)
    {
        if (empty($address_id)) {
            return 0;
        } else {
            $address = self::find()->where([
                'address_id' => $address_id,
                'user_id' => $user_id,
            ])->one();

            if ($address) {
                if ($address->consignee && $address->province && $address->city && $address->address &&
                    $address->mobile) {
                    return 2;
                } else {
                    return 1;
                }
            } else {
                return 0;
            }
        }
    }
}