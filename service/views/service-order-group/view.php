<?php

use service\assets\ServicerDivideDetailAsset;

/* @var $this yii\web\View */
/* @var $model common\models\OrderInfo */

$this->title = '订单详情';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/orderDetail';

ServicerDivideDetailAsset::register($this);

?>

<div class="wrapper wrapper-content animated fadeInUp">
    <div class="ibox">
        <div class="ibox-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="m-b-md">
                        <?php
                        echo \common\models\OrderGroup::$group_status_detail_cs_map[$model->group_status];
                        ?>

                        <h3>订单编号:<?= $model->group_id ?></h3>
                    </div>
                    <dl class="dl-horizontal">
                        <dt>店铺名称</dt>
                        <dd>
                            <span class="text-pink"><?= $model->users->company_name ?></span>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5">
                    <dl class="dl-horizontal">
                        <dt>收货人</dt> <dd><?= $model->consignee ?></dd>
                        <dt>联系电话</dt> <dd><?= $model->mobile ?></dd>
                        <dt>收货地址</dt> <dd><?= \common\models\Region::getUserAddress($model). ' '. $model->address ?></dd>
                        <dt>业务员</dt> <dd><?php echo !empty($model->users->servicerUser) ? $model->users->servicerUser->nickname: '未知业务员'; ?></dd>
                        <dt>商品总数</dt> <dd><?= $model->allGoodsNumber ?>个</dd>
                        <dt>已发货总数</dt> <dd><?= $model->allSendNumber ?>个</dd>
                    </dl>
                </div>
                <div class="col-lg-7" id="cluster_info">
                    <dl class="dl-horizontal">
                        <dt>下单时间:</dt> <dd><?= \common\helper\DateTimeHelper::getFormatCNDateTime($model->create_time) ?></dd>
                        <dt>订单总金额</dt> <dd>￥<?= \common\helper\NumberHelper::price_format($model->goods_amount + $model->shipping_fee - $model->discount) ?> </dd>
                        <?php if(Yii::$app->user->can('service_boss')):?>
                        <dt>提成总金额</dt> <dd>￥<?= \common\helper\NumberHelper::price_format($model->alreadyDivide)?> </dd>
                        <dt>已产生提成</dt> <dd>￥<?= \common\helper\NumberHelper::price_format($model->bossAlreadyDivide)  ?></dd>
                        <dt><?php echo !empty($model->users->servicerUser) ? $model->users->servicerUser->nickname: '未知业务员'; ?>已产生的提成</dt> <dd>￥<?= \common\helper\NumberHelper::price_format($model->servicerAlreadyDivide)  ?></dd>
                        <?php endif;?>
                    </dl>
                </div>
                <div class="row">
                    <div class="col-lg-12" style="margin-left:15px;">
                        <dl class="dl-horizontal">
                            <dt>物流单号</dt>
                            <dd>
                                <?php
                                    foreach($model->orders as $orderInfoList)
                                    {
                                        foreach($orderInfoList->deliveryOrder as $deliveryorder)
                                        {
                                            echo '<span>'.$deliveryorder->invoice_no.'</span> ';
                                        }
                                    }
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <dl class="dl-horizontal">
                        <dt>发货进度</dt>
                        <dd>
                            <?php if($model->IsAllShipped) {
                                echo '<!--全部发货start--><div class="progress progress-striped active m-b-sm">
                            <div style="width: 100%;" class="progress-bar pb-navy"></div>
                            </div>
                            <small>该订单已全部发货。亲，请耐心等待物流君的送达，小美感谢一路有您！</small><!--全部发货end-->';
                            } else {
                                echo '<!--部分发货start--><div class="progress progress-striped active m-b-sm">
                                <div style="width:'.$model->Progress.'%;" class="progress-bar pb-pink"></div>
                            </div>
                            <small>该订单已发货<strong>'.ceil($model->progress). '%</strong>。亲，小美正在加紧跟进中，爱你哟！</small>
                            <!--部分发货end-->';
                            }
                            ?>
                        </dd>
                    </dl>
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
                                    <li class="active"><a href="project_detail.html#tab-2" data-toggle="tab">订单商品</a></li>
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
                                            <th>数量</th>
                                            <th>小计</th>
                                            <th>已发货数量</th>
                                            <th>待发货数量</th>
                                            <th>物流单号</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($model->orders as $orderInfo):?>
                                            <?php foreach($orderInfo->ordergoods as $orderGoods):?>
                                            <tr>
                                                <td>
                                                    <?php if($orderGoods->send_number == $orderGoods->goods_number) {
                                                        echo '<span class="label label-primary"><i class="fa fa-check"></i>全部发货</span>';
                                                    } elseif($orderGoods->send_number == 0) {
                                                        echo '<span class="label label-danger"><i class="fa fa-check"></i>待发货</span>';
                                                    } else {
                                                        echo '<span class="label label-danger"><i class="fa fa-check"></i>部分发货</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="tb-goods">
                                                        <img src="<?= \common\helper\ImageHelper::get_image_path($orderGoods->goods->goods_thumb) ?>">
                                                        <span><?= $orderGoods->goods_name ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?=
                                                    //商品数量
                                                    ''.$orderGoods->goods_number. ($orderGoods->goods->measure_unit ?: '')
                                                    ?>
                                                </td>
                                                <td>
                                                    ￥<?=
                                                    //小计
                                                    ($orderGoods->goods_number)*($orderGoods->goods_price)
                                                    ?>
                                                </td>
                                                <td>
                                                <span class="tb-goods-green"><?=
                                                    //已发货数量
                                                    ''.$orderGoods->send_number. ($orderGoods->goods->measure_unit ?: '')
                                                    ?></span>
                                                </td>
                                                <td>
                                                <span class="tb-goods-red">
                                                    <?=
                                                    //待发货数量
                                                    ''.$orderGoods->goods_number - $orderGoods->send_number. ($orderGoods->goods->measure_unit ?: '')
                                                    ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?=
                                                    //物流信息
                                                    $orderGoods->getShippingInfo()
                                                    ?>
                                                </td>
                                            </tr>
                                                <?php endforeach;?>
                                        <?php endforeach;?>
                                        </tbody>
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