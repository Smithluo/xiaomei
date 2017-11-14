<?php

use yii\helpers\Html;
use common\widgets\GridView;
use service\assets\ServicerDivideAsset;
use common\models\OrderInfo;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ServicerDivideRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/orderList';

ServicerDivideAsset::register($this);

?>

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
    <?= GridView::widget([
        'showFooter' => true,                    //使用前端分页 shiningxiao
        'dataProvider' => $dataProvider,
        'dataColumnClass' => \common\widgets\DataColumn::className(),
        'columns' => [
            [
                'label'=>'订单号',
                'encodeLabel' => false,
                'attribute'=>'order_sn',
                'format'=>'html',
                'value'=>function($model) {
                    return $model->order_sn;
                },
                'filter'=>Html::activeTextInput($searchModel, 'order_sn', ['class'=>'form-control']),
                'footer' => '
                                    <td colspan="11">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                ',
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'下单时间',
                'encodeLabel' => false,
                'attribute'=>'add_time',
                'value'=>function($model) {
                    return \common\helper\DateTimeHelper::getFormatCNDateTime($model->add_time);
                },
                'filter'=>Html::activeTextInput($searchModel, 'add_time', ['class'=>'form-control']),
                'headerOptions'=>['data-hide'=>'phone'],
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'收货人',
                'encodeLabel' => false,
                'attribute'=>'consignee',
                'filter'=>Html::activeTextInput($searchModel, 'consignee', ['class'=>'form-control']),
                'headerOptions'=>['data-hide'=>'phone'],
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'收货人电话',
                'encodeLabel' => false,
                'attribute'=>'mobile',
                'filter'=>Html::activeTextInput($searchModel, 'mobile', ['class'=>'form-control']),
                'headerOptions'=>['data-hide'=>'phone'],
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'收货地址',
                'attribute'=>'address',
                'value'=>function($model) {
                    return \common\models\Region::getUserAddress($model). ' '. $model->address;
                },
                'filter'=>Html::activeTextInput($searchModel, 'add_time', ['class'=>'form-control']),
                'headerOptions'=>['data-hide'=>'phone'],
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'店铺名称',
                'encodeLabel' => false,
                'format' => 'raw',
                'attribute'=>'company_name',
                'value'=>function($model) {
                    if($model->user_id == 0 || $model->users == null) {
                        return '';
                    }
                    return $model->users->company_name;
                },
                'headerOptions'=>['data-hide'=>'all'],
                'contentOptions'=>['style'=>'display:none;'],
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'订单总金额',
                'encodeLabel' => false,
                'attribute'=>'goods_amount',
                'value'=>function($model) {
                    return \common\helper\NumberHelper::price_format($model->goods_amount - $model->discount);
                },
                'filter'=>Html::activeTextInput($searchModel, 'goods_amount', ['class'=>'form-control']),
                'headerOptions'=>['data-hide'=>'phone,tablet'],
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'业务员',
                'encodeLabel' => false,
                'attribute'=>'servicer_user_name',
                'value'=>function($model) {
                    if (!empty($model->servicerDivideRecord)) {
                        return $model->servicerDivideRecord[0]['servicer_user_name'];
                    }
                    return '未知';
                },
                'filter'=>Html::activeTextInput($searchModel, 'servicer_user_name', ['class'=>'form-control']),
                'headerOptions'=>['data-hide'=>'phone,tablet'],
                'enableSorting' => false, //客户端分页
            ],
//            [
//                'label'=>'业务员提成',
//                'encodeLabel' => false,
//                'attribute'=>'divide_amount',
//                'value'=>function($model) {
//                    return \common\helper\NumberHelper::price_format($model->divide_amount);
//                },
//                'filter'=>Html::activeTextInput($searchModel, 'divide_amount', ['class'=>'form-control']),
//                'headerOptions'=>['data-hide'=>'phone,tablet'],
//
//                'enableSorting' => false, //客户端分页
//            ],
            [
                'label'=>'订单状态',
                'encodeLabel' => false,
                'attribute'=>'order_status',
                'format' => 'raw',
                'value'=>function($model) {
                    $csStatus = OrderInfo::getOrderCsStatus(
                        [
                            'order_status' => $model->order_status,
                            'shipping_status' => $model->shipping_status,
                            'pay_status' => $model->pay_status,
                        ]);
                    return $csStatus;
                },
                'filter'=>Html::activeTextInput($searchModel, 'order_status', ['class'=>'form-control']),
                'headerOptions'=>['data-hide'=>'phone'],
                'enableSorting' => false, //客户端分页
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return '<div class="btn-group" >'. Html::a(
                            '订单详情',
                            $url,
                            [
                                'class' => 'btn btn-outline btn-primary',
                            ]
                        ).'</div>';
                    },
                ],
            ],

//            [
//                'class' => common\widgets\ActionColumn::className(),
//                'header' => '操作',
//                'template' => '{inCash}',
//                'buttons' => [
//                    'inCash' => function ($url, $model) {
//                        $result = '<div class="btn-group">';
//                        if($model->money_in_record_id > 0) {
//                            $result .= Html::button('已提取', ['class' => 'btn btn-outline btn-default', 'type' => 'button', 'xm-data'=>'id='.$model->id, 'xm-action'=>"getCash", 'data-original-title'=>'', 'title'=>'', 'disabled'=>'disabled'
//                            ]);
//                        }
//                        else {
//                            if($model->orderInfo->order_status == OrderInfo::ORDER_STATUS_REALLY_DONE) {
//                                $result .= Html::button('提取到钱包', ['class' => 'btn btn-outline btn-danger', 'type' => 'button', 'xm-data'=>'id='.$model->id, 'xm-action'=>"getCash"
//                                ]);
//                            }
//                            else {
//                                return '';
//                            }
//                        }
//                        $result .= '</div>';
//                        return $result;
//                    },
//                ],
//                'headerOptions'=>['class' => 'text-right'],
//                'contentOptions'=>['class' => 'text-right'],
//                'footer' => '',
//            ],
        ],
    ]); ?>
        </div></div></div>
