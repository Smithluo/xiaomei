<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/7
 * Time: 11:36
 */

namespace common\helper;

use Yii;
use common\models\BrandUser;

class SessionHelper
{
    /**
     * SessionHelper constructor.
     * 用户已登录则开始session
     */
    public function __construct()
    {
        if (Yii::$app->user->isGuest) {
            redirect('/index.php');
        } else {
            $session = Yii::$app->session;
            if (!$session->isActive) {
                $session->open();
            }
        }
    }
    /**
     * 获取登录用户下的品牌列表
     */
    public static function getUserBrandList()
    {
        $session = Yii::$app->session;
        $session->set('user_brand_list', BrandUser::getBrandIdList());  //  当前用户旗下的品牌id数组
    }

}