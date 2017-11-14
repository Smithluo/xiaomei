<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/4/28
 * Time: 13:46
 */

namespace api\modules\v2\controllers;


use api\modules\v1\controllers\BaseAuthActiveController;

class OrderController extends BaseAuthActiveController
{
    public function actionCheckout()
    {
        //  【0】基础参数
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');
        $extensionCode  = !empty($params['extensionCode']) ? $params['extensionCode'] : 0;
        $addressId      = !empty($params['addressId']) ? $params['addressId'] : 0;
        $prepay         = !empty($params['prepay']) ? $params['prepay'] : 0;
        $couponId       = !empty($params['couponId']) ? $params['couponId'] : 0;
        $actId          = !empty($params['actId']) ? $params['actId'] : 0;
        $goodsNum       = !empty($params['goodsNum']) ? $params['goodsNum'] : 0;
        $goodsId        = !empty($params['goodsId']) ? $params['goodsId'] : 0;

        //  【1】校验地址
        $validAddress = OrderGroupHelper::checkAddress($userModel->user_id, $addressId);
        if (!empty($validAddress)) {
            $addressCheck = true;
        } else {
            $addressCheck = false;
        }

        //  【2】区分购买方式 校验订单
        $rs = OrderGroupHelper::checkoutGoods(
            $params['userId'],
            $params['extensionCode'],
            $validAddress,
            $prepay,
            $couponId,
            $actId,
            $params['buy_goods_num'],
            $params['buy_goods_id']
        );
        $cartGoods = $rs['cartGoods'];
        $total = $rs['total'];

        //  【3】处理异常


        //  【4】返回数据
        return [
            'addressCheck'      => $addressCheck,
            '$artGoods'         => $cartGoods,
            'total'             => $total,  //  费用总计
        ];

    }
}