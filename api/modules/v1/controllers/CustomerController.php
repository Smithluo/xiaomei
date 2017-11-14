<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/28
 * Time: 11:10
 */

namespace api\modules\v1\controllers;

use \Yii;
use api\modules\v1\models\OrderInfo;
use api\modules\v1\models\Users;

class CustomerController extends BaseAuthActiveController
{
    public $modelClass = 'api\modules\v1\models\Users';
    /**
     * PUT customer/upgrade 用户支付完成后调用升级VIP接口。   上线时执行一次把没有升级的用户升级
     *
     * 验证当前用户等级
     *      未审核通过 => 返回 illegal
     *      注册会员 => 验证用户支付总金额是否超过 10000，超过则置为VIP会员，返回 yes 否则 返回 no
     *      VIP会员 或 SVIP会员 => 返回 been
     */
    public function actionUpgrade()
    {
        //  【1】初始化数据
        $userModel = Yii::$app->user->identity;

        //  【2】验证用户是否通过审核
        if ($userModel->is_checked == Users::IS_CHECKED_STATUS_PASSED) {
            if ($userModel->user_rank == Users::USER_RANK_REGISTED) {
                $totalAmount = OrderInfo::getUserTotalAmount($userModel->user_id);

                if ($totalAmount >= 10000) {
                    $userModel->user_rank = Users::USER_RANK_MEMBER;
                    if ($userModel->save()) {
                        return 'yes';
                    }
                } else {
                    return 'no';
                }
            } elseif ($userModel->user_rank > Users::USER_RANK_REGISTED) {
                return 'been';
            }
        }
        //  注册会员 和 审核未通过的用户不能变更等级
        return 'illegal';
    }

    /**
     * 绑定服务商
     */
    public function actionBind_service() {

    }

}