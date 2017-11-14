<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class BrandUser extends Users
{
    public function rules()
    {
        return [
            [['sex', 'pay_points', 'rank_points', 'address_id', 'zone_id', 'reg_time', 'last_login', 'visit_count',
                'user_rank', 'is_special', 'parent_id', 'flag', 'is_validated', 'servicer_user_id', 'servicer_super_id', 'servicer_info_id', 'servicer_super_id', 'is_checked'], 'integer'],
            [['parent_id', 'servicer_super_id', 'bank_info_id'], 'default', 'value'=>0],
            [['birthday', 'last_time', 'is_checked'], 'safe'],
            [['user_money', 'frozen_money', 'credit_line'], 'number'],
            [['user_name', 'password', 'mobile_phone', 'company_name', 'brand_id_list'], 'required'],
            [['alias', 'msn', 'qq', 'office_phone', 'home_phone', 'company_name', 'mobile_phone', 'qq_open_id', 'unionid', 'wx_pc_openid', 'licence_image'], 'default', 'value'=>''],
            [['credit_line'], 'default', 'value'=>0],
            [['email', 'alias', 'msn'], 'string', 'max' => 60],
            ['email', 'email'],
            [['user_name', 'question', 'answer', 'company_name', 'passwd_answer', 'headimgurl', 'licence_image',
                'brand_id_list', 'access_token'], 'string', 'max' => 255],
            [['nickname'], 'string', 'max' => 255],
            [['password', 'auth_key'], 'string', 'max' => 32],
            [['last_ip'], 'string', 'max' => 15],
            [['ec_salt', 'salt'], 'string', 'max' => 10],
            [['qq', 'office_phone', 'home_phone', 'mobile_phone'], 'string', 'max' => 20],
            [['passwd_question', 'openid', 'qq_open_id', 'aite_id', 'unionid', 'wx_pc_openid'], 'string', 'max' => 50],
            [['user_name'], 'unique', 'message'=>'用户名已被注册，请重新输入。'],
            [['mobile_phone'], 'unique', 'message'=>'手机号已被注册，请重新输入。'],
        ];
    }

    /**
     * 所有的供应品牌，包括指定brand和goods的列表
     * @param string $user_id
     * @return array|bool
     */
    public static function getBrandIdList($user_id = '')
    {
        if (!$user_id) {
            $user_id = Yii::$app->user->identity->getId();
        }

        $result = array_unique(ArrayHelper::merge(self::getGoodsBrandList($user_id), self::getSupplierBrandIdList($user_id)));
        if (empty($result)) {
            return false;
        }
        return $result;
    }

    /**
     * 获取直接指定供应商的品牌ID列表，即brand_id_list按逗号分割
     * @param $user_id
     * @return array
     */
    public static function getSupplierBrandIdList($user_id) {
        $rs = static::find()->where(['user_id' => $user_id])->one();
        if (isset($rs) && $rs->brand_id_list) {
            $result = explode(',', $rs->brand_id_list);
            return $result;
        }
        return [];
    }

    /**
     * 指定了供应商的商品的品牌ID集合
     * @param $user_id
     * @return array
     */
    public static function getGoodsBrandList($user_id) {
        //某些商品可能指定了供应商
        $goodsBrandIdList = array_unique(array_column(Goods::find()->select('brand_id')->where([
            'supplier_user_id' => $user_id,
            'is_on_sale' => 1,
            'is_delete' => 0,
        ])->asArray()->all(), 'brand_id'));

        return array_diff($goodsBrandIdList, self::getSupplierBrandIdList($user_id));
    }

    public function validatePassword($password) {
        if(!$this->brand_id_list) {
            return false;
        }

        return parent::validatePassword($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * 获取用户信息的指定列
     * @param $column
     */
    public static function getUserInfo($columns = '')
    {
        if ($columns && is_array($columns)) {
            $select_columns = implode(',', $columns);
        } elseif ($columns) {
            $select_columns = $columns;
        } else {
            $select_columns = '*';
        }
        return self::find()->select($select_columns)->where(['user_id' => Yii::$app->user->identity->getId()])->one();
    }

}
