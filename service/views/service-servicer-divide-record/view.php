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
                            if ($model->order_status == \common\models\OrderInfo::ORDER_STATUS_REALLY_DONE &&
                                $model->pay_status == \common\models\OrderInfo::PAY_STATUS_PAYED &&
                                $model->shipping_status == \common\models\OrderInfo::SHIPPING_STATUS_RECEIVED) {
                                echo '<span class="btn btn-w-m btn-primary od-status">已完成</span>';
                            }
                            else {
                                echo '<span class="btn btn-w-m btn-danger od-status">处理中</span>';
                            }
                        ?>

                        <h3>订单编号:<?= $model->order_sn ?></h3>
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
                        <dt>业务员</dt> <dd><?php echo !empty($model->servicerDivideRecord) ? $model->servicerDivideRecord[0]->servicer_user_name: '未知业务员'; ?></dd>
                        <dt>商品总数</dt> <dd><?= $model->getOrderGoodsCount() ?>个</dd>
                        <dt>已发货总数</dt> <dd><?= $model->getDeliveryGoodsCount() ?>个</dd>
                    </dl>
                </div>
                <div class="col-lg-7" id="cluster_info">
                    <dl class="dl-horizontal">
                        <dt>下单时间:</dt> <dd><?= \common\helper\DateTimeHelper::getFormatCNDateTime($model->add_time) ?></dd>
                        <dt>订单总金额</dt> <dd>￥<?= \common\helper\NumberHelper::price_format($model->getTotalAmount()) ?> </dd>
                        <dt>提成总金额</dt> <dd>￥<?= \common\helper\NumberHelper::price_format($model->getTotalDivideAmount()) ?> </dd>
                        <dt>已产生提成</dt> <dd>￥<?= \common\helper\NumberHelper::price_format($model->getAlreadyTotalDivideAmount()) ?> <span class="text-pink" style="margin-left:10px">(已发货的商品才会产生提成)</span> </dd>
                        <dt><?php echo !empty($model->servicerDivideRecord) ? $model->servicerDivideRecord[0]->servicer_user_name: '未知业务员'; ?>已产生的提成</dt> <dd>￥<?= \common\helper\NumberHelper::price_format($model->getAlreadyDivideAmount()) ?><span class="text-pink" style="margin-left:10px">(已发货的商品才会产生提成)</span></dd>
                    </dl>
                </div>
                <div class="row">
                    <div class="col-lg-12" style="margin-left:15px;">
                        <dl class="dl-horizontal">
                            <dt>物流单号</dt>
                            <dd>
                                <?php foreach($model->deliveryOrder as $deliveryOrder):?>
                                    <span><?= $deliveryOrder->invoice_no?></span>
                                <?php endforeach;?>
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
                           <?php if($model->isAllGoodsShipped()) {
                                echo '<!--全部发货start--><div class="progress progress-striped active m-b-sm">
                            <div style="width: 100%;" class="progress-bar pb-navy"></div>
                            </div>
                            <small>该订单已全部发货。亲，请耐心等待物流君的送达，小美感谢一路有您！</small><!--全部发货end-->';
                           } else {
                               echo '<!--部分发货start--><div class="progress progress-striped active m-b-sm">
                                <div style="width:'. round($model->getDeliveryGoodsCount() / $model->getOrderGoodsCount() * 100) .'%;" class="progress-bar pb-pink"></div>
                            </div>
                            <small>该订单已发货<strong>'.round($model->getDeliveryGoodsCount() / $model->getOrderGoodsCount() * 100) . '%</strong>。亲，小美正在加紧跟进中，爱你哟！</small>
                            <!--部分发货end-->';
                           }
                           ?>
                        </dd>
                    </dl>
                </div>
            </div>
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
                                            <th>已产生提成</th>
                                            <th>物流单号</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($model->ordergoods as $ordergoods):?>
                                        <tr>
                                            <td>
                                                <?php if($ordergoods->send_number == $ordergoods->goods_number) {
                                                    echo '<span class="label label-primary"><i class="fa fa-check"></i>全部发货</span>';
                                                } elseif($ordergoods->send_number == 0) {
                                                    echo '<span class="label label-danger"><i class="fa fa-check"></i>待发货</span>';
                                                } else {
                                                    echo '<span class="label label-danger"><i class="fa fa-check"></i>部分发货</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="tb-goods">
                                                    <img src="<?= \common\helper\ImageHelper::get_image_path($ordergoods->goods->goods_thumb) ?>">
                                                    <span><?= $ordergoods->goods_name ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?=
                                                //商品数量
                                                ''.$ordergoods->goods_number. ($ordergoods->goods->measure_unit ?: '')
                                                ?>
                                            </td>
                                            <td>
                                                ￥<?=
                                                //小计
                                                ($ordergoods->goods_number)*($ordergoods->goods_price)
                                                ?>
                                            </td>
                                            <td>
                                                <span class="tb-goods-green"><?=
                                                    //已发货数量
                                                    ''.$ordergoods->send_number. ($ordergoods->goods->measure_unit ?: '')
                                                    ?></span>
                                            </td>
                                            <td>
                                                <span class="tb-goods-red">
                                                    <?=
                                                    //待发货数量
                                                    ''.$ordergoods->goods_number - $ordergoods->send_number. ($ordergoods->goods->measure_unit ?: '')
                                                    ?>
                                                    </span>
                                            </td>
                                            <td>
                                                ￥
                                                <?=
                                                //已产生提成
                                                \common\helper\NumberHelper::price_format($ordergoods->getTotalDivideAmount())
                                                ?>
                                            </td>
                                            <td>
                                                <?=
                                                //物流信息
                                                $ordergoods->getShippingInfo()
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach;?>
                                        <!--<tr>
                                            <td>
                                                <span class="label label-danger"><i class="fa fa-check"></i> 部分发货</span>
                                            </td>
                                            <td>
                                                <div class="tb-goods">
                                                    <img src="http://img.xiaomei360.com/images/201604/thumb_img/239_thumb_G_1461374714324.jpg">
                                                    <span>澳洲莉莉蜜丽Lilly&amp;Milly 麦卢卡蜂蜜羊奶皂 100g</span>
                                                </div>
                                            </td>
                                            <td>
                                                100盒
                                            </td>
                                            <td>
                                                ￥2000.00
                                            </td>
                                            <td>
                                                <span class="tb-goods-green">50盒</span>
                                            </td>
                                            <td>
                                                <span class="tb-goods-red">50盒</span>
                                            </td>
                                            <td>
                                                ￥10.00
                                            </td>
                                            <td>
                                                德邦19823211001
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="label label-primary">
                                                    <i class="fa fa-check"></i>全部发货
                                                </span>
                                            </td>
                                            <td>
                                                <div class="tb-goods">
                                                    <img src="http://img.xiaomei360.com/images/201611/thumb_img/796_thumb_G_1479858039191.jpg">
                                                    <span>韩国SNP斯内普海洋燕窝水库面膜 10片/盒</span>
                                                </div>
                                            </td>
                                            <td>
                                                100盒
                                            </td>
                                            <td>
                                                ￥2000.00
                                            </td>
                                            <td>
                                                <span class="tb-goods-green">100盒</span>
                                            </td>
                                            <td>
                                                <span class="tb-goods-red">0盒</span>
                                            </td>
                                            <td>
                                                ￥10.00
                                            </td>
                                            <td>
                                                天地华宇12232323001
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                                <span class="label label-primary">
                                                                    <i class="fa fa-check"></i>全部发货
                                                                </span>
                                            </td>
                                            <td>
                                                <div class="tb-goods">

                                                    <img src="http://img.xiaomei360.com/images/201611/thumb_img/796_thumb_G_1479858039191.jpg">
                                                    <i class="tb-gift">赠品</i>
                                                    <span>韩国SNP斯内普海洋燕窝水库面膜 10片/盒</span>
                                                </div>
                                            </td>
                                            <td>
                                                10盒
                                            </td>
                                            <td>
                                                ￥0.00
                                            </td>
                                            <td>
                                                <span class="tb-goods-green">100盒</span>
                                            </td>
                                            <td>
                                                <span class="tb-goods-red">0盒</span>
                                            </td>
                                            <td>
                                                ￥0.00
                                            </td>
                                            <td>
                                                天地华宇12232323001
                                            </td>
                                        </tr>-->
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