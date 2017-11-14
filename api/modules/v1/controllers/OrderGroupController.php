<?php
/**
 * Created by PhpStorm.
 * User: Clark
 * Date: 2016-10-19
 * Time: 21:30
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\GoodsActivity;
use api\modules\v1\models\GoodsBuyForm;
use api\modules\v1\models\OrderGroup;
use api\modules\v1\models\OrderInfo;
use common\helper\DateTimeHelper;
use common\helper\UrlHelper;
use \Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;
use yii\rest\Serializer;
use yii\web\BadRequestHttpException;

class OrderGroupController extends BaseAuthActiveController
{

    public $modelClass = 'api\modules\v1\models\OrderGroup';

    /**
     * POST order/group-list   获取分组订单列表
     *
     * $params = [
     *      type => string, //  【必填】in_array(type, ['all', 'needPay', 'needReceive', 'refuse'])
     *      pageIndex => int,    //  页码
     *      pageSize => int, //  每页数量
     * ];
     *
     * return [
     *      count = [
     *          'all' => int
     *          'needPay' => int
     *          'needReceive' => int
     *          'refuse' => int
     *      ],              //  分类别计数
     *      'items' => []   //  要显示的结果集合
     *      '_links' => []  //  分页信息
     *      '_meta' => []   //  页码总数
     * ]
     */
    public function actionList() {

        $userModel = Yii::$app->user->identity;
        $data = Yii::$app->request->post('data');

        $types = [
            'all',
            'needPay',
            'needReceive',
            'refuse',
        ];

        Yii::info('data = '. VarDumper::export($data), __METHOD__);

        $page = (isset($data['pageIndex']) && is_numeric($data['pageIndex'])) ? $data['pageIndex']: 1;
        $size = (isset($data['pageSize']) && is_numeric($data['pageSize'])) ? $data['pageSize']: 5;
        $type = (isset($data['type']) && in_array($data['type'], $types)) ? $data['type']: 'all';

        Yii::info('page = '. $page. ', size = '. $size. ', type = '. $type, __METHOD__);

        $query = OrderGroup::find()->joinWith([
            'orders orders',
            'orders.brand brand',
            'orders.orderGoods orderGoods',
            'orders.orderGoods.event event',
        ])->where([
            OrderGroup::tableName().'.user_id' => $userModel['user_id'],
        ])->orderBy([
            'id' => SORT_DESC,
        ])->groupBy('id');

        if ($type === 'needPay') {
            Yii::info('待支付的订单', __METHOD__);
            $query->andWhere([
                'group_status' => OrderGroup::ORDER_GROUP_STATUS_UNPAY,
            ]);
        } elseif ($type === 'needReceive') {
            Yii::info('待收货的订单', __METHOD__);
            //  needReceive 订单综合状态：5待收货
            $query->andWhere([
                'group_status' => [
                    OrderGroup::ORDER_GROUP_STATUS_PAID,
                    OrderGroup::ORDER_GROUP_STATUS_HANDLING,
                    OrderGroup::ORDER_GROUP_STATUS_SHIPPING_FINISH,
                ],
            ]);
        } elseif ($type === 'refuse') {
            Yii::info('退换中的订单', __METHOD__);
            //  refuse  订单状态：[7,4,13]退换中
            $query->andWhere([
                'orders.order_status' => [
                    OrderInfo::ORDER_STATUS_RETURNED,
                    OrderInfo::ORDER_STATUS_ASK_4_REFUND,
                    OrderInfo::ORDER_STATUS_ASK_4_RETURN,
                    OrderInfo::ORDER_STATUS_AGREE_RETURN
                ]
            ]);
        }

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $size,
                'page' => $page - 1,
            ]
        ]);

        //  【3】构建provider
        $serializer = new Serializer();
        $serializer->collectionEnvelope = 'items';
        $result = $serializer->serialize($provider);

        Yii::info('result = '. VarDumper::export($result), __METHOD__);

        //  【4】格式化结果集
        $expiredMap = [];
        foreach ($result['items'] as $key => $orderGroup) {
            $result['items'][$key]['orders'] = OrderInfo::orderListFormat($orderGroup['orders'], $type);

            if ($type === 'needPay' || $orderGroup['group_status'] == OrderGroup::ORDER_GROUP_STATUS_UNPAY) {
                $temp = OrderInfo::checkOrderExtension($orderGroup['orders']);

                if (!empty($temp)) {
                    foreach ($temp as $key => $value) {
                        $expiredMap[$key] = $value;
                    }
                }
            }
        }
        $expiredMap = array_filter($expiredMap);
        Yii::warning(__LINE__.' $expiredMap = '.json_encode($expiredMap));

//        $list = OrderInfo::orderListFormat($result['items'], $type);
        $links = UrlHelper::formatLinks($result['_links']);


        //  【5】获取各类订单的数量
        $count = [];
        //  全部
        $count['all'] = OrderGroup::find()->where([
            OrderGroup::tableName().'.user_id' => $userModel['user_id'],
        ])->groupBy('id')->count();
        //  待支付
        $count['needPay'] = OrderGroup::find()->joinWith(['orders orders'])->where([
            OrderGroup::tableName().'.user_id' => $userModel['user_id'],
        ])->andWhere([
            'group_status' => OrderGroup::ORDER_GROUP_STATUS_UNPAY,
        ])->groupBy('id')->count();
        //  待收货
        $count['needReceive'] = OrderGroup::find()->joinWith(['orders orders'])->where([
            OrderGroup::tableName().'.user_id' => $userModel->user_id
        ])->andWhere([
            'group_status' => [
                OrderGroup::ORDER_GROUP_STATUS_PAID,
                OrderGroup::ORDER_GROUP_STATUS_HANDLING,
                OrderGroup::ORDER_GROUP_STATUS_SHIPPING_FINISH,
            ],
        ])->groupBy('id')->count();
        //  退换中
        $count['refuse'] = OrderGroup::find()->joinWith(['orders orders'])->where([
            OrderGroup::tableName().'.user_id' => $userModel->user_id
        ])->andWhere([
            'orders.order_status' => [
                OrderInfo::ORDER_STATUS_RETURNED,
                OrderInfo::ORDER_STATUS_ASK_4_REFUND,
                OrderInfo::ORDER_STATUS_ASK_4_RETURN,
                OrderInfo::ORDER_STATUS_AGREE_RETURN
            ],
        ])->groupBy('id')->count();

        Yii::info('总数：'. VarDumper::export($count), __METHOD__);

        return [
            'count' => $count,
            'items' => $result['items'],
            'expiredMap' => $expiredMap,
            '_links' => $links,
            '_meta' => $result['_meta'],
        ];
    }

    /**
     * POST order/list   获取整单详情
     *
     * $params = [
     *      group_id => string, //  【必填】in_array(type, ['all', 'needPay', 'needReceive', 'refuse'])
     * ];
     */
    public function actionView($group_id)
    {
        $userModel = Yii::$app->user->identity;

        $orderGroup = OrderGroup::find()->with([
            'orders',
            'orders.orderGoods',
            'deliveryOrders',
            'backOrders',
        ])->where([
            'group_id' => $group_id,
        ])->andWhere([
            'user_id' => $userModel->user_id,
        ])->one();

        if (empty($orderGroup)) {
            Yii::error('订单不存在 group_id = '. $group_id. ', user_id = '. $userModel->user_id, __METHOD__);
            throw new BadRequestHttpException('您请求的订单不存在', 3);
        }

        return $orderGroup;
    }

    /**
     * POST order-group/cancel  取消订单
     *
     * $param = [
     *      'group_id' => string,   //  总单号
     *      'note' => string,       //  备注
     * ]
     */
    public function actionCancel()
    {
        $userModel = Yii::$app->user->identity;
        $data = Yii::$app->request->post('data');

        if (empty($data['group_id'])) {
            throw new BadRequestHttpException('缺少必要参数', 1);
        } else {
            $orderGroup = OrderGroup::find()
                ->where([
                    'group_id' => $data['group_id'],
                    'user_id' => $userModel->id,
                ])->one();

            if (!empty($orderGroup)) {
                $orderGroup->cancel($data['note']);
            } else {
                throw new BadRequestHttpException('非法操作', 2);
            }
        }
        return [
            'message' => '订单已取消',
        ];
    }
}