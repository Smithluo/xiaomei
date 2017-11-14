<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\OrderInfo;
use common\models\Region;
use common\models\Brand;
use kartik\detail\DetailView;
use common\helper\NumberHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-info-form">

    <?php if (!empty($actions)): ?>
        <div class="row">

            <?php foreach ($actions as $action => $title) : ?>
                <div class="col-lg-6">
                <?php $form = ActiveForm::begin([
                    'action' => [$action],
                    'method' => 'post',
                ]); ?>
                备注：<?= Html::input('text', 'note', null, ['style' => 'width: 45%']) ?>
                <?php if ($action == 'shipping') {echo '物流单号：'. Html::input('text', 'shippingInfo');}?>
                <?= Html::input('hidden', 'orderId', $model->order_id) ?>
                <?= Html::submitButton($title, ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end(); ?>
                </div>
            <?php endforeach ?>

            <?php
            if (($model->order_status == OrderInfo::ORDER_STATUS_CONFIRMED
                && $model->pay_status == OrderInfo::PAY_STATUS_PAYED
                && $model->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED)
                ||
                ($model->order_status == OrderInfo::ORDER_STATUS_SPLITED
                && $model->pay_status == OrderInfo::PAY_STATUS_PAYED
                && $model->shipping_status == OrderInfo::SHIPPING_STATUS_SHIPPED_PART
                )
            ) {
                echo Html::a('自定义发货(发部分货)', \yii\helpers\Url::to([
                    '/order-info/advance-shipping',
                    'id' => $model->order_id
                ]), [
                    'target' => '_blank',
                    'class' => 'btn btn-success',
                ]);
            }
            ?>
        </div>
    <?php endif ?>

    <table class="table">
        <tbody>
        <tr>
            <th>操作者</th>
            <th>操作时间</th>
            <th>订单状态</th>
            <th>付款状态</th>
            <th>发货状态</th>
            <th>备注</th>
        </tr>
        <?php foreach ($model->orderAction as $action): ?>
            <tr>
                <th><?= $action->action_user ?></th>
                <th><?= \common\helper\DateTimeHelper::getFormatCNDateTime($action->log_time) ?></th>
                <th><?= OrderInfo::$order_status_map[$action->order_status] ?></th>
                <th><?= OrderInfo::$pay_status_map[$action->pay_status] ?></th>
                <th><?= OrderInfo::$shipping_status_map[$action->shipping_status] ?></th>
                <th><?= $action->action_note ?></th>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <?php

    echo Html::a('打印订单', ['print', 'id' => $model->order_id], ['target' => '_blank']);

    //商品列表
    $orderGoods = [];
    foreach ($model->ordergoods as $goods) {
        $orderGoods[]['columns'] = [
            [
                'label' => '商品类型',
                'displayOnly' => true,
                'attribute' => 'is_gift',
                'format' => 'raw',
                'value' => !empty($isGiftStyleMap[$goods->is_gift])
                    ? $isGiftStyleMap[$goods->is_gift]
                    : '<span class="text-error">错误类型</span>' ,
                'labelColOptions' => [
                    'style' => 'width: 4%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 6%',
                ],
                'viewModel' => $goods->goods,
                'editModel' => $goods->goods,
                'displayOnly' => true,
            ],
            [
                'label' => '商品缩略图',
                'displayOnly' => true,
                'attribute' => 'goods_thumb',
                'format' => 'raw',
                'value' => \yii\helpers\Html::img(\common\helper\ImageHelper::get_image_path($goods->goods['goods_thumb']), [
                    'width' => '50px',
                    'height' => '50px',
                ]),
                'labelColOptions' => [
                    'style' => 'width: 6%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 4%',
                ],
                'viewModel' => $goods->goods,
                'editModel' => $goods->goods,
            ],
            [
                'label' => '商品名',
                'displayOnly' => true,
                'format' => 'raw',
                'attribute' => 'goods_name',
                'value' => Html::a($goods['goods_name'], \yii\helpers\Url::to(['/goods/view', 'id' => $goods['goods_id']]), ['target' => '_blank']),
                'labelColOptions' => [
                    'style' => 'width: 5%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 35%',
                ],
                'viewModel' => $goods,
                'editModel' => $goods,
            ],
            [
                'label' => '货号',
                'displayOnly' => true,
                'attribute' => 'goods_sn',
                'labelColOptions' => [
                    'style' => 'width: 3%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 7%',
                ],
                'viewModel' => $goods,
                'editModel' => $goods,
            ],
            [
                'label' => '单价',
                'displayOnly' => true,
                'attribute' => 'goods_price',
                'labelColOptions' => [
                    'style' => 'width: 3%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 7%',
                ],
                'viewModel' => $goods,
                'editModel' => $goods,
            ],
            [
                'label' => '数量',
                'displayOnly' => true,
                'attribute' => 'goods_number',
                'labelColOptions' => [
                    'style' => 'width: 3%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 7%',
                ],
                'viewModel' => $goods,
                'editModel' => $goods,
            ],
            [
                'label' => '小计',
                'displayOnly' => true,
                'value' => \common\helper\NumberHelper::price_format($goods->goods_number * $goods->goods_price),
                'labelColOptions' => [
                    'style' => 'width: 3%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 7%',
                ],
            ],
        ];
    }

    $attributes = \yii\helpers\ArrayHelper::merge([
        [
            'group' => true,
            'label' => '基本信息',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'order_sn',
                    'label' => '订单号',
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute'=>'order_status',
                    'format'=>'raw',
                    'value'=>'<kbd>'. OrderInfo::$order_status_map[$model->order_status]. ', '. OrderInfo::$pay_status_map[$model->pay_status]. ', '. OrderInfo::$shipping_status_map[$model->shipping_status] .'</kbd>',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                    'displayOnly'=>true
                ],
                [
                    'attribute' => 'consignee',
                    'label' => '下单人',
                    'value' => empty($model->users) ? '未找到此用户' : $model->users->showName,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'add_time',
                    'label' => '下单时间',
                    'value' => \common\helper\DateTimeHelper::getFormatCNDateTime($model->add_time),
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute'=>'pay_time',
                    'label' => '付款时间',
                    'value' => \common\helper\DateTimeHelper::getFormatCNDateTime($model->pay_time),
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                    'displayOnly'=>true
                ],
                [
                    'attribute' => 'shipping_time',
                    'label' => '发货时间',
                    'value' => \common\helper\DateTimeHelper::getFormatCNDateTime($model->shipping_time),
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'label' => '微信支付单号',
                    'value' => empty($model->wechatPayInfo) ? '' : $model->wechatPayInfo->out_trade_no,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'label' => '支付宝支付单号',
                    'value' => empty($model->alipayInfo) ? '' : $model->alipayInfo->out_trade_no,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                    'displayOnly'=>true
                ],
                [
                    'label' => '银联支付单号',
                    'value' => empty($model->yinlianPayInfo) ? '' : $model->yinlianPayInfo->out_trade_no,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'label' => '易宝支付单号',
                    'value' => empty($model->yeePayInfo) ? '' : $model->yeePayInfo->out_trade_no,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'label' => '配送方式',
                    'value' => $model->shipping_name,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'label' => '物流单号：',
                    'value' => $model->invoice_no,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'group' => true,
            'label' => '收货人信息',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'label' => '收货人',
                    'displayOnly' => true,
                    'attribute' => 'consignee',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'label' => '手机号码',
                    'attribute' => 'mobile',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                ],
                [
                    'label' => '省',
                    'displayOnly' => true,
                    'attribute' => 'province',
                    'value' => Region::getRegionName($model->province),
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => Region::getProvinceMap(),
                        'options' => ['placeholder' => '选择省'],
                        'pluginOptions' => ['width' => '100%'],
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'label' => '市',
                    'displayOnly' => true,
                    'attribute' => 'city',
                    'value' => Region::getRegionName($model->city),
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => Region::getCityMap($model->province),
                        'options' => ['placeholder' => '选择城市'],
                        'pluginOptions' => ['width' => '100%'],
                    ],
                ],
                [
                    'label' => '区',
                    'displayOnly' => true,
                    'attribute' => 'district',
                    'value' => Region::getRegionName($model->district),
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => Region::getCityMap($model->city),
                        'options' => ['placeholder' => '选择区'],
                        'pluginOptions' => ['width' => '100%'],
                    ],
                ],
                [
                    'label' => '地址',
                    'attribute' => 'address',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'label' => '客户备注',
                    'attribute' => 'postscript',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 90%',
                    ],
                ],
            ],
        ],
        [
            'group' => true,
            'label' => '商品信息',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
    ], $orderGoods);

    $attributes = \yii\helpers\ArrayHelper::merge($attributes, [
        [
            'group' => true,
            'label' => '费用信息',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'label' => '货款',
                    'displayOnly' => true,
                    'value' => $model->goods_amount,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                ],
                [
                    'label' => '配送费用',
                    'displayOnly' => true,
                    'value' => $model->shipping_fee,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'label' => '折扣',
                    'attribute' => 'discount',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'label' => '订单总金额',
                    'displayOnly' => true,
                    'value' => NumberHelper::price_format($model->getTotalAmount()),
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'label' => '已支付金额',
                    'displayOnly' => true,
                    'attribute' => 'money_paid',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                ],
                [
                    'label' => '待支付金额',
                    'displayOnly' => true,
                    'attribute' => 'order_amount',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
    ]);

    $deliveryOrderAttributes = [
        [
            'group' => true,
            'label' => '发货单信息',
            'rowOptions' => [
                'class' => 'info',
            ]
        ]
    ];
    foreach ($model->deliveryOrder as $deliveryOrder) {
        $deliveryOrderAttributes[] = [
            'group' => true,
            'label' => '发货单：'. $deliveryOrder->delivery_sn. '， 快递单号：'. $deliveryOrder->invoice_no,
            'rowOptions' => [
                'class' => 'info',
            ]
        ];

        if (!empty($deliveryOrder->servicerDivideRecord)) {
            $deliveryOrderAttributes[] = [
                'columns' => [
                    [
                        'attribute' => 'divide_amount',
                        'label' => '业务员分成金额',
                        'displayOnly' => true,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 20%',
                        ],
                        'viewModel' => $deliveryOrder->servicerDivideRecord,
                        'editModel' => $deliveryOrder->servicerDivideRecord,
                    ],
                    [
                        'attribute' => 'parent_divide_amount',
                        'label' => '服务商分成金额',
                        'displayOnly' => true,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 20%',
                        ],
                        'viewModel' => $deliveryOrder->servicerDivideRecord,
                        'editModel' => $deliveryOrder->servicerDivideRecord,
                    ],
                ]
            ];
        }

        foreach ($deliveryOrder->deliveryGoods as $deliveryGoods) {
            $deliveryOrderAttributes[] = [
                'columns' => [
                    [
                        'attribute' => 'goods_sn',
                        'label' => '商品货号',
                        'displayOnly' => true,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'viewModel' => $deliveryGoods,
                        'editModel' => $deliveryGoods,
                    ],
                    [
                        'attribute' => 'goods_name',
                        'label' => '商品名称',
                        'displayOnly' => true,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 30%',
                        ],
                        'viewModel' => $deliveryGoods,
                        'editModel' => $deliveryGoods,
                    ],
                    [
                        'attribute' => 'goods_price',
                        'label' => '单价',
                        'displayOnly' => true,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'viewModel' => $deliveryGoods,
                        'editModel' => $deliveryGoods,
                    ],
                    [
                        'attribute' => 'send_number',
                        'label' => '发货数量',
                        'displayOnly' => true,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'viewModel' => $deliveryGoods,
                        'editModel' => $deliveryGoods,
                    ],
                ],
            ];
        }
    }

    $attributes = \yii\helpers\ArrayHelper::merge($attributes, $deliveryOrderAttributes);

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'mode' => DetailView::MODE_VIEW,
        'deleteOptions'=>[ // your ajax delete parameters
            'params' => ['id' => $model->order_id, 'custom_param' => true],
        ],
        'panel'=>[
            'heading'=>'订单：' . $model->order_sn,
            'type'=>DetailView::TYPE_PRIMARY,
        ],
        'buttons1' => '{update}',
        'formOptions' => [
            'action' => \yii\helpers\Url::to(['update', 'id' => $model->order_id]),
        ],
        'fadeDelay' => 100,
    ]);
    ?>
</div>
