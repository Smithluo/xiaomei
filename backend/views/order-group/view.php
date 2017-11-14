<?php

use backend\assets\OrderGroupDetailAsset;
use common\helper\ImageHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderGroup */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

OrderGroupDetailAsset::register($this);

?>
<div class="gray-bg">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2></h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?= Url::to(['/site/index']) ?>">小美诚品管理员后台</a>
                </li>
                <li>
                    <a href="<?= Url::to(['/order-group/index']) ?>">订单详情</a>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    <!--<span class="btn btn-w-m btn-primary od-status">已完成</span>-->
                                    <!--<span class="btn btn-w-m btn-danger od-status">处理中</span>-->
                                    <h3>订单编号:<?= $model->group_id ?></h3>
                                </div>
                                <dl class="dl-horizontal">
                                    <dt>店铺名称</dt>
                                    <dd>
                                        <span class="text-pink">
                                            <?= empty($model->users) ? '' : \yii\helpers\Html::a($model->users->company_name, Url::to([
                                                '/sc-user/view', 'id' => $model->user_id,
                                            ]), [
                                                'target' => '_blank',
                                            ]) ?>
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <dl class="dl-horizontal">
                                    <dt>用户名</dt> <dd><?= $model['users']['user_name']. '('. $model['users']['nickname']. ')('. $model['users']['mobile_phone']. ')' ?></dd>
                                    <dt>收货人</dt> <dd><?= $model->consignee ?></dd>
                                    <dt>联系电话</dt> <dd><?= $model->mobile ?></dd>
                                    <dt>收货地址</dt> <dd><?= \common\models\Region::getAddress($model, $model['address']) ?></dd>
                                    <dt>订单归属</dt>
                                    <dd>
                                        <?php
                                        //看谁命中这个区域
                                        $managerUser = \common\models\Users::find()->joinWith([
                                            'userRegion userRegion',
                                        ])->where([
                                            'userRegion.region_id' => [
                                                $model->users->province,
                                                $model->users->city,
                                            ],
                                        ])->andWhere([
                                            'servicer_info_id' => 0,
                                        ])->one();

                                        if (!empty($managerUser)) {
                                            echo $managerUser->showName. '('. $managerUser->mobile_phone. ')';
                                        }
                                        else {
                                            echo '吴喜芝(13049889166)';
                                        }

                                        ?>
                                    </dd>
                                    <dt>订单状态</dt>
                                    <dd>
                                        <div class="btn-group">
                                            <button class="btn-white btn btn-xs"><?= \common\models\OrderGroup::$order_group_status[$model->group_status] ?></button>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-lg-4" id="cluster_info">
                                <dl class="dl-horizontal" >
                                    <dt>下单时间</dt> <dd><?= \common\helper\DateTimeHelper::getFormatCNDateTime($model->create_time) ?></dd>
                                    <dt>付款时间</dt> <dd><?= \common\helper\DateTimeHelper::getFormatCNDateTime($model->pay_time) ?></dd>
                                    <dt>微信支付单号</dt> <dd><?= empty($model->wechatPayInfo) ? '' : $model->wechatPayInfo->out_trade_no ?> </dd>
                                    <dt>支付宝支付单号</dt> <dd><?= empty($model->alipayInfo) ? '' : $model->alipayInfo->out_trade_no ?> </dd>
                                    <dt>易宝支付单号</dt> <dd><?= empty($model->yeePayInfo) ? '' : $model->yeePayInfo->out_trade_no ?> </dd>
                                </dl>
                            </div>
                            <div class="col-lg-4 super-option">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>操作区域</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <!--<button type="button" class="btn btn-w-m btn-default"></button>-->
                                        <?php if (Yii::$app->user->can('/order-group/shipping')): ?>
                                            <button type="button" class="btn btn-w-m btn-primary" xm-node="sendAll">快速发货</button>
                                        <?php endif; ?>
                                        <?php if (Yii::$app->user->can('/order-group/advance-shipping')): ?>
                                            <button type="button" class="btn btn-w-m btn-danger" xm-node="customSend">自定义发货</button>
                                        <?php endif; ?>
                                        <?php if (Yii::$app->user->can('/order-group/pay')): ?>
                                            <button type="button" class="btn btn-w-m btn-info" xm-node="payOrder">支付订单</button>
                                        <?php endif; ?>
                                        <?php if (Yii::$app->user->can('/order-group/refund')): ?>
                                            <button type="button" class="btn btn-w-m btn-warning" xm-node="refund">退款退货</button>
                                        <?php endif; ?>
                                        <?php if (Yii::$app->user->can('/order-group/cancel')): ?>
                                            <button type="button" class="btn btn-w-m btn-default" xm-node="cancel">取消订单</button>
                                        <?php endif; ?>
                                        <?php if (Yii::$app->user->can('/order-group/shipped')): ?>
                                            <button type="button" class="btn btn-w-m btn-white" xm-node="endOrder">发货完结</button>
                                        <?php endif; ?>
                                        <a href="<?= Url::to(['/order-group/index']) ?>" class="btn btn-w-m btn-pink">返回订单列表</a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-8" style="margin-left:15px;">
                                    <dl class="dl-horizontal">
                                        <dt>物流单号</dt>
                                        <dd>
                                            <?php foreach ($model->deliveryOrders as $deliveryOrder): ?>
                                            <span><?= $deliveryOrder->invoice_no ?></span>
                                            <?php endforeach; ?>
                                        </dd>
                                    </dl>
                                </div>
                            </div>

                            <?php if (Yii::$app->user->can('/order-group/modify-user') && $model->group_status == \common\models\OrderGroup::ORDER_GROUP_STATUS_UNPAY || $model->group_status == \common\models\OrderGroup::ORDER_GROUP_STATUS_PAID): ?>
                            <button type="button" class="btn btn-w-m btn-primary" xm-node="changeOrderUser">修改订单归属</button>
                            <?php endif; ?>

                            <?php if (Yii::$app->user->can('/order-group/modify') && $model->group_status == \common\models\OrderGroup::ORDER_GROUP_STATUS_UNPAY): ?>
                                <button type="button" class="btn btn-w-m btn-success" xm-action="modify" xm-data="url=/order-group/modify">修改订单商品</button>
                            <?php endif; ?>

                            <?php if (Yii::$app->user->can('/order-group/force-modify') && $model->group_status == \common\models\OrderGroup::ORDER_GROUP_STATUS_UNPAY): ?>
                                <button type="button" class="btn btn-w-m btn-danger" xm-action="modify" xm-data="url=/order-group/force-modify">强制修改订单商品</button>
                            <?php endif; ?>

                        </div>
                        <div class="forum-item active">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="forum-icon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <a href="javascript:;" class="forum-item-title">订单费用信息</a>
                                    <div class="forum-sub-title">此总单的所有货款、物流费、折扣信息 </div>
                                </div>
                                <div class="col-md-2 forum-info">
                                    <span class="views-number">
                                        <?php
                                            if ($integralOrder) {
                                                echo (int)$model->goods_amount.'积分';
                                            } else {
                                                echo '￥'.$model->goods_amount;
                                            }
                                        ?>
                                    </span>
                                    <div>
                                        <small>货款</small>
                                    </div>
                                </div>
                                <div class="col-md-2 forum-info">
                                    <span class="views-number">
                                        ￥<?= $model->shipping_fee ?>
                                    </span>
                                    <div>
                                        <small>配送费用</small>
                                    </div>
                                </div>
                                <div class="col-md-2 forum-info">
                                    <span class="views-number">
                                        ￥<?= $model->discount ?>
                                        <?php
                                            if (!empty($model->event)) {
                                                echo '('. $model->event_id. ')'. $model['event']['event_name'];
                                            }
                                        ?>
                                    </span>
                                    <div>
                                        <small>折扣</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="forum-item active">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="forum-icon">
                                        <i class="fa fa-shield"></i>
                                    </div>
                                    <a href="javascript:;" class="forum-item-title">订单支付金额</a>
                                    <div class="forum-sub-title">此总单的最终总金额、已支付金额、待支付金额 </div>
                                </div>
                                <div class="col-md-2 forum-info">
                                    <span class="views-number">
                                        <?php
                                        $amount = $model->getTotalFee();
                                        if ($integralOrder) {
                                            echo (int)$amount.'积分';
                                        } else {
                                            echo '￥'.$amount;
                                        }
                                        ?>
                                    </span>
                                    <div>
                                        <small>订单总金额</small>
                                    </div>
                                </div>
                                <div class="col-md-2 forum-info">
                                    <span class="views-number">
                                        ￥<?= $model->getMoneyPaid() ?>
                                    </span>
                                    <div>
                                        <small>已支付金额</small>
                                    </div>
                                </div>
                                <div class="col-md-2 forum-info">
                                    <span class="views-number">
                                        ￥<?= $model->getTotalOrderAmount() ?>
                                    </span>
                                    <div>
                                        <small>待支付金额</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php if (!empty($model->deliveryOrders)): ?>
                        <div class="row m-t-sm">
                            <div class="col-lg-12">
                                <div class="panel blank-panel">
                                    <div class="panel-heading">
                                        <div class="panel-options">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="javascript:;" data-toggle="tab">物流信息</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="tab-content">
                                            <div class="tab-pane active" >
                                                <table class="table table-striped logistsc_table">
                                                    <thead>
                                                    <tr>
                                                        <th>物流单号</th>
                                                        <th>运费</th>
                                                        <th>发货时间</th>
                                                        <th class="text-center">物流跟踪</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    <?php foreach ($model->deliveryOrders as $deliveryOrder): ?>
                                                    <tr>
                                                        <td>
                                                            <?= $deliveryOrder->invoice_no ?>
                                                        </td>
                                                        <td>
                                                            <?= \common\helper\NumberHelper::price_format($deliveryOrder->shipping_fee) ?>
                                                        </td>
                                                        <td>
                                                            <?= \common\helper\DateTimeHelper::getFormatCNDateTime($deliveryOrder->add_time) ?>
                                                        </td>
                                                        <td class="text-center logistsc_pro">
                                                            <button type="button" class="btn btn-w-m btn-info" xm-node="view_logistsc" xm-data="<?= $deliveryOrder->delivery_id ?>">查看物流信息</button>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="row m-t-sm">
                            <div class="col-lg-12">
                                <div class="panel blank-panel">
                                    <div class="panel-heading">
                                        <div class="panel-options">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="javascript:;" data-toggle="tab">订单商品</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="panel-body">

                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab-2">

                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>状态</th>
                                                        <th>商品名称</th>
                                                        <th>商品货号</th>
                                                        <th>单价</th>
                                                        <th>实付单价</th>
                                                        <th>数量</th>
                                                        <th>小计</th>
                                                        <th>小样配比</th>
                                                        <th>已发货数量</th>
                                                        <th>待发货数量</th>
                                                        <th>物流单号</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <!--子单分割容器@garaaluo-->
                                                    <?php foreach ($model->orders as $order): ?>
                                                    <tr>
                                                        <td colspan="11" class="childOrder">
                                                            <span> 分单号：<?= \yii\helpers\Html::a($order->order_sn, Url::to([
                                                                    '/order-info/view', 'id' => $order->order_id,
                                                                ]), [
                                                                    'target' => '_blank',
                                                                ]) ?></span> ——
                                                            <span> 配送信息：<?= $order->shipping_name ?></span>
                                                        </td>
                                                    </tr>
                                                        <?php foreach ($order->ordergoods as $ordergoods): ?>
                                                            <tr>
                                                                <td>
                                                                    <span class="label label-primary">
                                                                        <i class="fa fa-check"></i><?= $ordergoods->getShippingStatus() ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <div class="tb-goods">
                                                                        <img src="<?= \common\helper\ImageHelper::get_image_path($ordergoods->goods->goods_thumb) ?>" />
                                                                        <span>
                                                                            <a target="_blank" href="<?= Url::to(['/goods/view', 'id' => $ordergoods->goods_id]) ?>"><?= 'rec_id:('. $ordergoods->rec_id. ')'. $ordergoods->goods_name ?> 锁库存量：<?= $ordergoods['goods']->getLockCount() ?></a>
                                                                            <?php if ($ordergoods->goods->goods_number < $ordergoods->goods_number): ?>
                                                                                <p style="background-color: #ff950c">当前库存量：<?= $ordergoods->goods->goods_number ?></p>
                                                                            <?php else: ?>
                                                                                <p>当前库存量：<?= $ordergoods->goods->goods_number ?></p>
                                                                            <?php endif ?>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <?= $ordergoods->goods_sn ?>
                                                                </td>
                                                                <td>
                                                                    ￥<?= $ordergoods->goods_price ?>
                                                                </td>
                                                                <td>
                                                                    ￥<?= $ordergoods->pay_price ?>
                                                                </td>
                                                                <td>
                                                                    x<?= $ordergoods->goods_number ?>
                                                                </td>
                                                                <td>
                                                                    ￥<?= ($ordergoods->goods_number * $ordergoods->goods_price) ?>
                                                                </td>
                                                                <td>
                                                                    <?= $ordergoods->sample ?>
                                                                </td>
                                                                <td>
                                                                    <span class="tb-goods-green">x<?= $ordergoods->send_number ?></span>
                                                                </td>
                                                                <td>
                                                                    <span class="tb-goods-red">x<?= $ordergoods->goods_number - $ordergoods->send_number - $ordergoods->back_number ?></span>
                                                                </td>
                                                                <td>
                                                                    <?= $ordergoods->getShippingInfo() ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($model->backOrderList)): ?>
                        <div class="row m-t-sm">
                            <div class="col-lg-12">
                                <div class="panel blank-panel">
                                    <div class="panel-heading">
                                        <div class="panel-options">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="javascript:;" data-toggle="tab">退货单</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="panel-body">

                                        <div class="tab-content">
                                            <div class="tab-pane active">

                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>商品名称</th>
                                                        <th>数量</th>
                                                        <th>小计</th>
                                                        <th>退货数量</th>
                                                        <th>退货小计</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($model->backOrderList as $backOrder): ?>
                                                        <?php foreach ($backOrder->backGoods as $backGoods): ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="tb-goods">
                                                                        <img src="<?= $backGoods->getGoodsThumb() ?>" />
                                                                        <span><?= $backGoods->goods_name ?></span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    x<?= $backGoods->getOrderGoodsNumber() ?>
                                                                </td>
                                                                <td>
                                                                    ￥<?= \common\helper\NumberHelper::price_format($backGoods->goods_price * $backGoods->getOrderGoodsNumber()) ?>
                                                                </td>
                                                                <td>
                                                                    <span class="tb-goods-green">x<?= $backGoods->send_number ?></span>
                                                                </td>
                                                                <td>
                                                                    <span class="tb-goods-green">x<?= \common\helper\NumberHelper::price_format($backGoods->send_number * $backGoods->goods_price) ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="row optionList">
                            <div class="col-lg-12">
                                <div class="ibox">
                                    <div class="ibox-content">
                                        <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="5">
                                            <thead>
                                            <tr>
                                                <th>
                                                    子单号<span class="footable-sort-indicator"></span>
                                                </th>
                                                <th data-hide="phone">
                                                    操作者<span class="footable-sort-indicator"></span>
                                                </th>
                                                <th data-hide="phone">
                                                    操作时间<span class="footable-sort-indicator"></span>
                                                </th>
                                                <th data-hide="phone">
                                                    订单状态<span class="footable-sort-indicator"></span>
                                                </th>
                                                <th data-hide="phone">
                                                    付款状态<span class="footable-sort-indicator"></span>
                                                </th>
                                                <th>
                                                    发货状态<span class="footable-sort-indicator"></span>
                                                </th>
                                                <th class="text-right">
                                                    备注<span class="footable-sort-indicator"></span>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($model->orders as $order): ?>
                                                <?php foreach ($order->orderAction as $action): ?>
                                            <tr>
                                                <td>
                                                    <?= $order->order_sn ?>
                                                </td>
                                                <td>
                                                    <?= $action->action_user ?>
                                                </td>
                                                <td>
                                                    <?= \common\helper\DateTimeHelper::getFormatCNDateTime($action->log_time) ?>
                                                </td>
                                                <td>
                                                    <?= \common\models\OrderInfo::$order_status_map[$action->order_status] ?>
                                                </td>
                                                <td>
                                                    <?= \common\models\OrderInfo::$pay_status_map[$action->pay_status] ?>
                                                </td>
                                                <td>
                                                    <?= \common\models\OrderInfo::$shipping_status_map[$action->shipping_status] ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= $action->action_note ?>
                                                </td>
                                            </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="6">
                                                    <ul class="pagination pull-right"></ul>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 快速发货 -->
<div class="modal fade in" id="sendAll" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= Url::to(['/order-group/shipping']) ?>" method="post" id="sendAllForm">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
            <input type="hidden" name="id" value="<?= $model->id ?>">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header text-center">
                    <h4 class="modal-title">快速发货-所有商品齐备可发</h4>
                    <small class="font-bold"></small>
                </div>
                <div class="modal-body">
                    <!--子单容器@garaaluo-->
                    <?php foreach ($model->orders as $order): ?>
                    <div class="childSendAll">
                        <div class="panel-heading childTitle">
                            <i class="fa fa-info-circle"></i> 分单号<?= $order->order_sn ?>
                        </div>
                        <div class="childImgs">
                            <?php foreach ($order->ordergoods as $ordergoods):?>
                            <img src="<?= empty($ordergoods->goods) ? '' : ImageHelper::get_image_path($ordergoods->goods->goods_thumb) ?>">
                            <?php endforeach; ?>
                        </div>
                        <div class="input-group date col-lg-4 input-fl" style="margin-right: 35px;">
                            <label>分单号<?= $order->order_sn ?>的物流单号</label>
                            <label class="error">物流单号不能为空</label>
                            <input type="text" name="data[<?= $order->order_id ?>][shippingInfo]" placeholder="分单号的物流单号" class="form-control">
                        </div>
                        <div class="input-group date col-lg-4 input-fl" style="margin-right: 35px;">
                            <label>分单号<?= $order->order_sn ?>的备注</label>
                            <label class="error">备注信息不能为空</label>
                            <input type="text" name="data[<?= $order->order_id ?>][note]" placeholder="请输入备注信息" class="form-control">
                        </div>
                        <div class="input-group date col-lg-3 input-fl">
                            <label>分单号<?= $order->order_sn ?>的运费</label>
                            <label class="error">备注信息不能为空</label>
                            <input type="text" name="data[<?= $order->order_id ?>][shippingFee]" placeholder="请输入实付运费" class="form-control">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-pink" xm-node="sendAllConfirm">确认发货</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 取消订单 -->
<div class="modal fade in" id="cancelOrder" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= Url::to('/order-group/cancel') ?>" method="post" id="cancelOrderForm">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header text-center">
                    <h4 class="modal-title">取消订单</h4>
                    <small class="font-bold"></small>
                </div>
                <div class="modal-body">
                    <div class="input-group date col-sm-12">
                        <label>请输入备注</label>
                        <label class="error">备注信息不能为空</label>
                        <input type="hidden" name="id" value="<?= $model->id ?>">
                        <input type="text" name="note" placeholder="请输入备注信息" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-pink" xm-node="cancelOrderConfirm">确认取消订单</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 支付订单 -->
<div class="modal fade in" id="payModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= Url::to('/order-group/pay') ?>" method="post" id="payModalForm">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header text-center">
                    <h4 class="modal-title">支付订单</h4>
                    <small class="font-bold"></small>
                </div>
                <div class="modal-body">
                    <div class="input-group date col-sm-12">
                        <label>请输入备注</label>
                        <label class="error">备注信息不能为空</label>
                        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
                        <input type="hidden" name="id" value="<?= $model->id ?>">
                        <input type="text"  name="note" placeholder="请输入备注信息" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-pink" xm-node="payModalConfirm">确认支付订单</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 自定义发货 -->
<div class="modal fade in" id="customModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-vlg">
        <form action="<?= Url::to('/order-group/advance-shipping') ?>" method="post" id="customModalForm">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
            <input type="hidden" name="id" value="<?= $model->id ?>">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header text-center">
                    <h4 class="modal-title">自定义发货</h4>
                    <small class="font-bold"></small>
                </div>
                <div class="modal-body">
                    <div class="panel-body">

                        <div class="tab-content">
                            <div class="tab-pane active" id="custom-tab">

                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>商品id</th>
                                        <th>商品名称</th>
                                        <th>货号</th>
                                        <th>订单数量</th>
                                        <th>待发数量</th>
                                        <th class="text-right">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!--子单分割容器 @garaaluo-->
                                    <?php foreach ($model->orders as $order): ?>
                                    <tr>
                                        <td colspan="6" class="childOrder">
                                            <span> 分单号：<?= $order->order_sn ?></span>
                                            <div class="input-group date col-sm-3 text-right" style="float:right" >
                                                <input type="text" name="data[<?= $order->order_id ?>][shippingInfo]" placeholder="请输入物流单号" class="form-control" xm-id="post_<?= $order->order_sn ?>">
                                            </div>
                                            <div class="input-group date col-sm-2 text-right" style="float:right;margin-right:10px;" >
                                                <input type="text" name="data[<?= $order->order_id ?>][shippingFee]" placeholder="请填入运费" class="form-control">
                                            </div>
                                        </td>
                                    </tr>
                                        <?php foreach ($order->ordergoods as $ordergoods): ?>
                                            <tr>
                                                <td>
                                                    <?= $ordergoods->goods_id ?>
                                                </td>
                                                <td>
                                                    <div class="tb-goods">
                                                        <img src="<?= $ordergoods->getGoodsThumb() ?>">
                                                        <span><?= $ordergoods->goods_name ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?= $ordergoods->goods_sn ?>
                                                </td>
                                                <td>
                                                    x<?= $ordergoods->goods_number ?>
                                                </td>
                                                <td>
                                                    x<?= $ordergoods->goods_number - $ordergoods->send_number - $ordergoods->back_number ?>
                                                </td>
                                                <td class="text-right">
                                                    <button type="button" class="btn btn-outline btn-primary" xm-action="skuSendAll" max-num="<?= $ordergoods->goods_number - $ordergoods->send_number - $ordergoods->back_number ?>">全部</button>
                                                    <div class="input-group date col-sm-6 text-right" style="float:right" >
                                                        <input type="text" name="data[<?= $order->order_id ?>][orderGoodsList][<?= $ordergoods->rec_id ?>]" placeholder="发货数量" class="form-control" xm-action="customInput" xm-id="<?= $order->order_sn ?>">
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>

                            </div>
                            <div class="input-group date col-sm-12" style="margin-top:20px;">
                                <label>请输入备注</label>
                                <label class="error">备注信息不能为空</label>
                                <input type="text" name="note" placeholder="请输入备注信息" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-pink" xm-node="customModalConfirm">确认生成发货单</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!--修改订单-->
<div class="modal fade in" id="modifyOrder" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-vlg">
        <form action="<?= Url::to(['/order-group/modify']) ?>" method="post" id="modifyOrderForm">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
            <input type="hidden" name="id" value="<?= $model->id ?>">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header text-center">
                    <h4 class="modal-title">修改订单</h4>
                    <small class="font-bold"></small>
                </div>
                <div class="modal-body">
                    <div class="forum-item active forum-border">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="forum-icon">
                                    <i class="fa fa-shield"></i>
                                </div>
                                <a href="javascript:;" class="forum-item-title">当前订单金额</a>
                                <div class="forum-sub-title">此总单的总金额(含运费、已优惠金额)、运费、已优惠金额 </div>
                            </div>
                            <div class="col-md-2 forum-info">
                                        <span class="views-number" id="order_toatl">
                                            ￥<?= $model->getTotalFee() ?>
                                        </span>
                                <div>
                                    <small>订单总金额</small>
                                </div>
                            </div>
                            <div class="col-md-2 forum-info" id="post_num">
                                        <span class="views-number">
                                            ￥<?= $model->shipping_fee ?>
                                        </span>
                                <div>
                                    <small>运费</small>
                                </div>
                            </div>
                            <div class="col-md-2 forum-info" id="sale_num">
                                        <span class="views-number">
                                            ￥<?= $model->discount ?>
                                        </span>
                                <div>
                                    <small>已优惠金额</small>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="input-group" style="width: 97%;margin-left:14px;">
                        <i class="clearable fa fa-remove clearPosition"></i>
                        <input type="text" class="form-control" id="modalTest_input" autocomplete="off" style="border-radius: 4px; background: rgb(255, 255, 255);" data-id="" alt="">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="" >
                                添加商品
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu" >
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane active tab-border">
                                <table class="table table-striped" xm-action="modifyTable">
                                    <thead>
                                    <tr>
                                        <th>商品id</th>
                                        <th>商品名称</th>
                                        <th>单价</th>
                                        <th>小计</th>
                                        <th class="text-right">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($model->orders as $order): ?>
                                        <?php foreach ($order->ordergoods as $ordergoods): ?>
                                            <tr>
                                                <td>
                                                    <?= $ordergoods->goods_id ?>
                                                </td>
                                                <td>
                                                    <div class="tb-goods">
                                                        <img src="<?= $ordergoods->getGoodsThumb() ?>">
                                                        <span><?= $ordergoods->goods_name. '('. $ordergoods->goods_sn. ')' ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group date col-sm-6 text-right" style="float:left" >
                                                        <input type="text" name="recIdList[<?= $ordergoods->rec_id ?>][goods_price]" placeholder="修改价格" value="<?= $ordergoods->goods_price ?>" xm-action="modify_price" class="form-control">
                                                    </div>
                                                </td>
                                                <td>
                                                    ￥<?= $ordergoods->getTotalAmount() ?>
                                                </td>
                                                <td class="text-right">
                                                    <button type="button" class="btn btn-outline btn-danger" style="float:right" xm-action="skuDelete" >删除</button>
                                                    <div class="input-group date col-sm-6 text-right" style="float:right" >
                                                        <input type="text" name="recIdList[<?= $ordergoods->rec_id ?>][goods_number]" placeholder="修改数量" value="<?= $ordergoods->goods_number ?>" max-num="<?= $ordergoods->goods->goods_number ?>" min-num="<?= $ordergoods->goods->start_num ?>" <?php if ($ordergoods->goods->buy_by_box): ?>box-num="<?= $ordergoods->goods->number_per_box ?>"<?php endif; ?> xm-action="modify_input" class="form-control" xm-id="<?= $ordergoods->goods_id ?>">
                                                    </div>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </div>
                    <div class="forum-item active forum-border">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="forum-icon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <a href="javascript:;" class="forum-item-title">新单费用说明</a>
                                <div class="forum-sub-title">此总单已支付金额，现总金额，待支付金额 </div>
                            </div>
                            <div class="col-md-2 forum-info">
                                    <span class="views-number" id="pay_number">
                                        ￥<?= $model->money_paid ?>
                                    </span>
                                <div>
                                    <small>客户已支付金额</small>
                                </div>
                            </div>
                            <div class="col-md-2 forum-info" >
                                    <span class="views-number" id="new_order_number">
                                        ￥<?= $model->getTotalFee() ?>
                                    </span>
                                <div>
                                    <small>新单总金额</small>
                                </div>
                            </div>
                            <div class="col-md-2 forum-info">
                                    <span class="views-number" id="need_to_pay_number">
                                        ￥<?= $model->getTotalOrderAmount() ?>
                                    </span>
                                <div>
                                    <small>客户待支付金额</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group date col-sm-11 text-left" style="margin-left:15px;" >
                        <label>请输入备注</label>
                        <label class="error">备注信息不能为空</label>
                        <input type="text" name="note" placeholder="请输入修改备注信息，不能为空" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-pink" xm-node="modifyConfirm">确认修改订单</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 申请退款退货 -->
<div class="modal fade in" id="refundModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-vlg">

        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header text-center">
                <h4 class="modal-title">退款退货</h4>
                <small class="font-bold"></small>
            </div>

            <div class="modal-body">
                <div class="radio radio-danger radio-inline" xm-action="refundType">
                    <input type="radio" id="inlineRadio1" value="0" name="radioInline" checked="">
                    <label for="inlineRadio1"> 部分退货申请 </label>
                </div>
                <div class="radio radio-danger radio-inline" xm-action="refundType">
                    <input type="radio" id="inlineRadio2" value="1" name="radioInline">
                    <label for="inlineRadio2"> 全部退款申请 </label>
                </div>
                <!--退款退货form容器-->
                <form action="<?= Url::to(['/order-group/refund'])?>" method="post" id="refundPartForm">

                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
                    <input type="hidden" name="id" value="<?= $model->id ?>">

                    <div class="tab-content" id="refundGoodsList">
                        <div class="tab-pane active" id="refund-tab">

                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>商品id</th>
                                    <th>商品名称</th>
                                    <th>货号</th>
                                    <th>订单数量</th>
                                    <th>待发数量</th>
                                    <th class="text-right">操作</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php foreach ($model->orders as $order): ?>
                                    <!--子单分割容器 @garaaluo-->
                                    <tr>
                                        <td colspan="6" class="refundOrder">
                                            <span> 分单号：<?= $order->order_sn ?></span>

                                        </td>
                                    </tr>
                                    <?php foreach ($order->ordergoods as $ordergoods): ?>
                                        <tr>
                                            <td>
                                                <?= $ordergoods->goods_id ?>
                                            </td>
                                            <td>
                                                <div class="tb-goods">
                                                    <img src="<?= $ordergoods->getGoodsThumb() ?>">
                                                    <span><?= $ordergoods->goods_name ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?= $ordergoods->goods_sn ?>
                                            </td>
                                            <td>
                                                x<?= $ordergoods->goods_number ?>
                                            </td>
                                            <td>
                                                x<?= $ordergoods->goods_number - $ordergoods->send_number - $ordergoods->back_number ?>
                                            </td>
                                            <td class="text-right">
                                                <button type="button" class="btn btn-outline btn-warning" xm-action="refundAll" max-num="<?= $ordergoods->goods_number - $ordergoods->back_number ?>">全部</button>
                                                <div class="input-group date col-sm-6 text-right" style="float:right" >
                                                    <input type="text" name="recIdList[<?= $order->order_id ?>][<?= $ordergoods->rec_id ?>]" placeholder="请输入退货数量" class="form-control" xm-action="refundInput">
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="input-group date col-sm-12" style="margin-top:20px;">
                        <label>请输入备注</label>
                        <label class="error">备注信息不能为空</label>
                        <input type="text" name="note" placeholder="请输入备注信息" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-pink" xm-node="refundModalConfirm">确认生成退货单</button>
            </div>
        </div>

    </div>
</div>
<!-- 发货完结 -->
<div class="modal fade in" id="endModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= Url::to(['/order-group/shipped']) ?>" method="post" id="endModalForm">

            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
            <input type="hidden" name="id" value="<?= $model->id ?>">

            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header text-center">
                    <h4 class="modal-title">发货完结</h4>
                    <small class="font-bold"></small>
                </div>
                <div class="modal-body">
                    <div class="input-group date col-sm-12">
                        <label>请输入备注</label>
                        <label class="error">备注信息不能为空</label>
                        <input type="text"  name="note" placeholder="请输入备注信息" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-pink" xm-node="endModalConfirm">确认发货完结</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!--修改订单归属用户-->
<div class="modal fade in" id="changeUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= Url::to('/order-group/modify-user') ?>" method="post" id="changeUserForm">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
            <input type="hidden" name="id" value="<?= $model->id ?>">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header text-center">
                    <h4 class="modal-title">修改订单归属人</h4>
                    <small class="font-bold"></small>
                </div>
                <div class="modal-body">

                    <div class="input-group" style="width: 92%;margin-left:14px;margin-top:10px;">
                        <i class="clearable fa fa-remove clearPosition"></i>
                        <input type="text" class="form-control" id="changerUser_input" autocomplete="off" style="border-radius: 4px; background: rgb(255, 255, 255);" data-id="" alt="" name="user_id">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="" >
                                选择归属用户
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu" >
                            </ul>
                        </div>
                    </div>

                    <div class="input-group date col-sm-11 text-left" style="margin-left:15px;margin-top:10px;" >
                        <label>收货人</label>
                        <label class="error">收货人不能为空</label>
                        <input type="text" name="consignee" placeholder="请输入收货人，不能为空" class="form-control">
                    </div>

                    <div class="input-group date col-sm-11 text-left" style="margin-left:15px;margin-top:10px;" >
                        <label>联系电话</label>
                        <label class="error">联系电话不能为空</label>
                        <input type="text" name="mobile" placeholder="请输入联系电话，不能为空" class="form-control">
                    </div>

                    <div class="input-group date col-sm-11 text-left" style="margin-left:15px;margin-top:10px;" >
                        <label style="display:block;">收货地址</label>
                        <select class="form-control m-b" name="province" xm-node="province" xm-data="province" style="width:32%;margin-right:2%">
                            <option>请选择省</option>
                            <?php foreach (\common\models\Region::getProvinceMap() as $k => $value): ?>
                                <option value="<?= $k ?>"><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select class="form-control m-b" name="city" xm-node="city" xm-data="city" style="width:32%;margin-right:2%">
                            <option>请选择城市</option>
                        </select>
                        <select class="form-control m-b" name="district" xm-node="area" xm-data="area" style="width:32%;">
                            <option>请选择区域</option>
                        </select>

                        <input type="text" name="address" placeholder="请输入详细地址" class="form-control" style="margin-top:10px;">
                    </div>

                    <div class="input-group date col-sm-11 text-left" style="margin-left:15px;margin-top:10px;" >
                        <label>请输入备注</label>
                        <label class="error">备注信息不能为空</label>
                        <input type="text" name="note" placeholder="请输入修改备注信息，不能为空" class="form-control">
                    </div>

                    <input type="hidden" name="user_id" id="select_user_id" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-pink" xm-node="changeUserConfirm">确认改变订单归属用户</button>
                </div>
            </div>
        </form>
    </div>
</div>




<script>
    $CONFIG["goodsData"] = <?= json_encode($goodsData) ?>;
    $CONFIG["userData"] = <?= json_encode($userData) ?>;
    <?php if(Yii::$app->session->hasFlash('failed')): ?>
    $CONFIG["errorTips"] = <?= Yii::$app->session->getFlash('failed', '', true) ?>;
    <?php endif; ?>
</script>



