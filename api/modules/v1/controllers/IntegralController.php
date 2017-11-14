<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/12/6
 * Time: 16:28
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\OrderInfo;
use common\helper\DateTimeHelper;
use common\helper\TextHelper;
use Yii;
use api\modules\v1\models\Integral;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

class IntegralController extends BaseAuthActiveController
{

    public $modelClass = 'api\modules\v1\models\Integral';

    /**
     * GET integral/list    获取用户的积分流水表和
     * @return array
     */
    public function actionList()
    {
        $userModel = \Yii::$app->user->identity;

        $list = Integral::getUserIntList($userModel->user_id);
        //  用户可用积分余额为0 则需要重新获取
        if ($userModel->int_balance == 0) {
            $balance = Integral::getBalance($userModel->user_id);

            if ($balance < 0) {
                $balance = 0;
                Yii::trace('用户ID为'.$userModel->user_id.'的积分 < 0');
            }
            if (!$userModel->save()) {
                \Yii::trace('ID为'.$userModel->user_id.'的用户变更积分余额入库失败');
            }
        } else {
            $balance = $userModel->int_balance;
        }

        return [
            'list' => $list,
            'balance' => $balance,
        ];
    }

    /**
     * GET integral/balance    获取用户的积分可用余额
     * @return array
     */
    public function actionBalance()
    {
        $userModel = \Yii::$app->user->identity;

        //  用户可用积分余额为0 则需要重新获取
        if ($userModel->int_balance == 0) {
            $balance = Integral::getBalance($userModel->user_id);

            if ($balance < 0) {
                $balance = 0;
                Yii::trace('用户ID为'.$userModel->user_id.'的积分 < 0');
            }
            $userModel->int_balance = $balance;
            if (!$userModel->save()) {
                \Yii::trace('ID为'.$userModel->user_id.'的用户变更积分余额入库失败');
            }

        } else {
            $balance = $userModel->int_balance;
        }

        return [
            'balance' => $balance,
        ];

        /*//  重新获取 ? 是否强制重新获取
        $balance = Integral::getBalance($userModel->user_id);
        if ($balance < 0) {
            $balance = 0;
            Yii::trace('用户ID为'.$userModel->user_id.'的积分 < 0');
        }
        $userModel->int_balance = $balance;
        if (!$userModel->save()) {
            \Yii::trace('ID为'.$userModel->user_id.'的用户变更积分余额入库失败');
        }*/
    }

    /**
     * POST integral/create 用户 支付/兑换 产生的交易流水
     *
     * 插入积分流水 不能掉用需要权限验证的接口
     * 积分计算  以实际支付金额为准， goods_amount - discount
     *
     * $data = [
     *      'integral'  int 积分数额
     *      'pay_code'  string  支付方式
     *      'out_trade_no'  string  第三方支付流水号
     *      'note'  string  积分流水备注，用户支付或下单生成的，应填写用户的订单id，如有多个 填写些group_id
     *      'status'  int [0,1]  积分状态
     * ]
     * @return array
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $userModel = \Yii::$app->user->identity;
        $data = Yii::$app->request->post('data');

        //  【1】验证参数
        Yii::trace('验证参数：Yii::$app->request->post(data) = '.json_encode($data));
        if (empty($data['integral'])) {
            Yii::error('缺少参数：积分数额integral');
            throw new BadRequestHttpException('缺少参数：积分数额', 1);
        }
        if (empty($data['pay_code'])) {
            Yii::error('缺少参数：支付方式pay_code');
            throw new BadRequestHttpException('缺少参数：支付方式', 2);
        }
        if (empty($data['out_trade_no'])) {
            Yii::error('缺少参数：第三方支付流水号out_trade_no');
            throw new BadRequestHttpException('缺少参数：第三方支付流水号', 3);
        }
        //  自然生成的流水，每个订单生成一条流水记录
        if (empty($data['note'])) {
            Yii::error('缺少参数：积分流水备注note');
            throw new BadRequestHttpException('缺少参数：积分流水备注', 4);
        } else {
            //  一个订单只能对应一条流水记录
            $integral = Integral::find()->where(['note' => $data['note']])->one();
            if ($integral) {
                Yii::error('缺少参数：该订单已有积分流水$integral = '.json_encode($integral));
                throw new BadRequestHttpException('非法操作：该订单已有积分流水', 11);
            } else {
                $order = OrderInfo::find()->where(['order_id' => $data['note']])->one();
                if (!$order) {
                    Yii::error('缺少参数：该订单不存在 order_id:'.$data['note']);
                    throw new BadRequestHttpException('非法操作：该订单不存在', 12);
                }
            }
        }
        if (empty($data['status'])) {
            $data['status'] = 0;
        }

        //  【2】流水入库
        $stamp = DateTimeHelper::getFormatGMTTimesTimestamp();

        $_model = [
            'integral' => $data['integral'],
            'user_id' => $userModel->user_id,
            'pay_code' => $data['pay_code'],
            'out_trade_no' => $data['out_trade_no'],
            'note' => $data['note'],
            'created_at' => $stamp,
            'updated_at' => $stamp,
            'status' => $data['status'],
        ];

        $model = new Integral();
        $model->setAttributes($_model);
        if ($model->validate() && $model->save()) {
            $id = Yii::$app->db->getLastInsertID();
            $userModel->int_balance = 0;
            if (!$userModel->save()) {
                Yii::trace('ID为'.$userModel->user_id.'的用户变更积分余额入库失败');
            }
            return $id;
        } else {
            Yii::trace('ID为'.$userModel->user_id.'的用户变更积分余额入库失败'.TextHelper::getErrorsMsg($model->errors));
            throw new ServerErrorHttpException(TextHelper::getErrorsMsg($model->errors), 1);
        }

    }

    /**
     * POST integral/edit    积分状态修改接口
     *
     * 冻结积分在订单终结时解冻，更新用户当前可用积分
     *
     * $data = [
     *      'note' => string 积分流水的note, 订单确认收货，如果下单时有拆单且一起支付一起确认，则为 group_id,单个订单则传order_id
     *      'status' => int [0,1]   积分状态  当前场景有： 冻结积分转为可用
     * ]
     * @return array
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionEdit()
    {
        $userModel = \Yii::$app->user->identity;
        $data = Yii::$app->request->post('data');
        $userId = $userModel->user_id;

        Yii::trace(__FILE__.__FUNCTION__.' integral/edit接口 开始 Yii::$app->request->post(data)').json_encode($data);
        if (empty($data['note'])) {
            Yii::error('缺少必要参数：积分流水的note');
            throw new BadRequestHttpException('缺少必要参数：积分流水的note', 1);
        }
        if (!isset($data['status'])) {
            Yii::error('缺少必要参数：积分状态status');
            throw new BadRequestHttpException('缺少必要参数：积分状态', 2);
        }

        $model = Integral::find()->where([
                'user_id' => $userId,
                'note' => $data['note'],
            ])->one();
        if (empty($model)) {
            Yii::error('非法请求：您当前没有对应的积分流水 $userId = '.$userId);
            throw new BadRequestHttpException('非法请求：您当前没有对应的积分流水', 3);
        }

        $stamp = DateTimeHelper::getFormatGMTTimesTimestamp();
        $model->setAttribute('updated_at', $stamp);
        $model->setAttribute('status', $data['status']);

        if ($model->save()) {
            $userModel->int_balance = 0;
            if (!$userModel->save()) {
                \Yii::trace('ID为'.$userModel->user_id.'的用户变更积分余额入库失败');
            }

            return true;

        } else {
            Yii::error('积分状态变更失败 $model->attributes = '.json_encode($model->attributes));
            throw new ServerErrorHttpException('积分状态变更失败', 6);
        }
    }
}