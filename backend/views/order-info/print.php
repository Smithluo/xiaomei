<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/15 0015
 * Time: 19:38
 */

use common\helper\NumberHelper;

?>

<style type="text/css">
    body,td { font-size:13px; }
</style>
<h1 align="center">订单信息</h1>
<table width="100%" cellpadding="1">
    <tr>
        <td width="8%">购 货 人：</td>
        <td><?= empty($model->users)?'':$model->users->showName ?><!-- 购货人姓名 --></td>
        <td align="right">下单时间：</td><td><?= \common\helper\DateTimeHelper::getFormatCNDateTime($model->add_time) ?><!-- 下订单时间 --></td>
        <td align="right">订单编号：</td><td><?= $model->order_sn ?><!-- 订单号 --></td>
    </tr>
    <tr>
        <td>付款时间：</td><td><?= \common\helper\DateTimeHelper::getFormatCNDateTime($model->pay_time) ?></td><!-- 付款时间 -->
        <td align="right">发货时间：</td><td><?= \common\helper\DateTimeHelper::getFormatCNDateTime($model->shipping_time) ?><!-- 发货时间 --></td>
        <td align="right">配送方式：</td><td><?= $model->shipping_name ?><!-- 配送方式 --></td>
        <td align="right">发货单号：</td><td><?= $model->invoice_no ?> <!-- 发货单号 --></td>
    </tr>
    <tr>
        <td>收货地址：</td>
        <td colspan="7">
    [<?= \backend\models\Region::getRegionName($model->country) ?>  <?= \backend\models\Region::getRegionName($model->province) ?>  <?= \backend\models\Region::getRegionName($model->city) ?>  <?= \backend\models\Region::getRegionName($model->district) ?>]&nbsp;<?= $model->address ?>&nbsp;<!-- 收货人地址 -->
        收货人：<?= $model->consignee ?> &nbsp;<!-- 收货人姓名 -->
        <!-- 邮政编码 -->
        <!-- 联系电话 -->
        手机：<?= $model->mobile ?><!-- 手机号码 -->
        </td>
    </tr>
</table>
<table width="100%" border="1" style="border-collapse:collapse;border-color:#000;">
    <tr align="center">
        <td bgcolor="#cccccc">商品名称  <!-- 商品名称 --></td>
        <td bgcolor="#cccccc">货号    <!-- 商品货号 --></td>
        <td bgcolor="#cccccc">属性  <!-- 商品属性 --></td>
        <td bgcolor="#cccccc">价格 <!-- 商品单价 --></td>
        <td bgcolor="#cccccc">数量<!-- 商品数量 --></td>
        <td bgcolor="#cccccc">小计    <!-- 价格小计 --></td>
    </tr>
    <!--  -->
    <?php foreach ($model->ordergoods as $goods): ?>
    <tr>
        <td>&nbsp;<?= $goods->goods_name ?><!-- 商品名称 -->
                        </td>
        <td>&nbsp;<?= $goods->goods_sn ?> <!-- 商品货号 --></td>
        <td><!-- 商品属性 -->
        <!--  -->
        <!--  -->
        <!--  -->
        </td>
        <td align="right"><?= $goods->goods_price ?>&nbsp;<!-- 商品单价 --></td>
        <td align="right"><?= $goods->goods_number ?>&nbsp;<!-- 商品数量 --></td>
        <td align="right"><?= NumberHelper::price_format($goods->goods_number * $goods->goods_price) ?>&nbsp;<!-- 商品金额小计 --></td>
    </tr>
    <?php endforeach ?>
    <!--  -->
    <tr>
        <!-- 发票抬头和发票内容 -->
        <td colspan="4">
                </td>
        <!-- 商品总金额 -->
        <td colspan="2" align="right">商品总金额：<?= $model->goods_amount ?></td>
    </tr>
</table>
<table width="100%" border="0">
    <tr align="right">
        <td>                                        <!-- 订单总金额 -->
        = 订单总金额：<?= NumberHelper::price_format($model->goods_amount + $model->shipping_fee - $model->discount) ?>        </td>
    </tr>
    <tr align="right">
        <td>
        <!-- 如果已付了部分款项, 减去已付款金额 -->
        - 已付款金额：<?= $model->money_paid ?>
<!-- 如果使用了余额支付, 减去已使用的余额 -->

        <!-- 如果使用了积分支付, 减去已使用的积分 -->

        <!-- 如果使用了红包支付, 减去已使用的红包 -->

        <!-- 应付款金额 -->
        = 应付款金额：<?= $model->order_amount ?>        </td>
    </tr>
</table>
<table width="100%" border="0">

    <tr><!-- 网店名称, 网店地址, 网店URL以及联系电话 -->
        <td>
小美诚品（http://www.xiaomei360.com）
        地址：广东省深圳市宝安区 宝安互联网产业基地 A区3栋4层3B06&nbsp;&nbsp;电话：18682415360        </td>
    </tr>
    <tr align="right"><!-- 订单操作员以及订单打印的日期 -->
        <td>打印时间：<?= \common\helper\DateTimeHelper::getFormatDateTime(time()) ?>&nbsp;&nbsp;&nbsp;操作者：<?= Yii::$app->user->identity['user_name'] ?></td>
    </tr>
</table>