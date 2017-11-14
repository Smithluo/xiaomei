<?php

use brand\models\OrderInfo;
use common\helper\DateTimeHelper;
use common\helper\NumberHelper;
use brand\models\BrandDivideRecord;
use common\models\ShopConfig;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OrderInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;

$this->params['ext_css'] = '<link href="http://adminjs.xiaomei360.com/components/supplier/order/order.css?version='.$r_version.'" type="text/css" rel="stylesheet">';

$this->params['ext_js'] = '<script src="http://adminjs.xiaomei360.com/lib/lib_base.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/lib/grid.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/app/supplier/order.js?version='.$r_version.'"></script>
<script>steel.boot("app/supplier/order");</script>';

$active_record_id = [];

if (isset($order_cs_status) && $order_cs_status) {
    $this->params['breadcrumbs'][] = $order_cs_status.'订单列表';
} else {
    $this->params['breadcrumbs'][] = '全部订单列表';
}
?>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'params' => $params
    ]); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">

                    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
                        <thead>
                        <tr>
                            <th>订单号</th>
                            <th data-hide="phone">支付时间</th>
                            <th data-hide="all">店铺名称</th>
                            <th data-hide="all">收货人</th>
                            <th data-hide="all">收货人电话</th>
                            <th data-hide="all">收货地址</th>
                            <th data-hide="phone,tablet" >订单总金额</th>
                            <th data-hide="phone,tablet" >交易手续费</th>
                            <th data-hide="phone,tablet" >服务商佣金</th>
                            <th data-hide="phone,tablet" >应收金额</th>
                            <th data-hide="phone">订单状态</th>
                            <th data-hide="phone">运费</th>
                            <th data-hide="all">订单详情</th>
                            <th class="text-right">操作</th>
                        </tr>

                        </thead>
                        <tbody>
                        <?php
                            foreach ($model_list as $model) :
                        ?>
                            <tr>
                                <td>
                                    <?=$model->order_sn?>
                                </td>
                                <td>
                                    <?=DateTimeHelper::getFormatCNDateTime($model->pay_time)?>
                                </td>
                                <td>
                                    <?=$order_goods[$model->order_id]['company_name']?>
                                </td>
                                <td>
                                    <?=$model->consignee?>
                                </td>
                                <td>
                                    <?=$model->mobile?>
                                </td>
                                <td>
                                    <?=$order_goods[$model->order_id]['address']?>
                                </td>
                                <td>
                                    <?= NumberHelper::format_as_money(bcadd($model->goods_amount, $model->shipping_fee, 4)) ?>
                                </td>
                                <td>
                                    <?= NumberHelper::format_as_money($order_goods[$model->order_id]['order_pay_fee']) ?>
                                </td>
                                <td>
                                    <?=NumberHelper::format_as_money($order_goods[$model->order_id]['server_need_pay'])?>
                                </td>
                                <td>
                                    <?=NumberHelper::format_as_money($order_goods[$model->order_id]['need_pay'])?>
                                </td>
                                <td>
                                    <?=$order_goods[$model->order_id]['cs_order_status']?>
                                </td>
                                <td>
                                    <span class="text-success">
                                        <?php
                                        if(isset($model->shipping->shipping_desc)) {
                                            echo $model->shipping->shipping_desc;
                                        }
                                        else {
                                            echo '发货方包邮';
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?=$order_goods[$model->order_id]['order_goods_list']?>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <?php
                                            //  待发货订单
                                            $to_be_shipped_status = OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED;
                                            if ($model->order_status == OrderInfo::ORDER_STATUS_SPLITED &&
                                                $model->shipping_status != OrderInfo::SHIPPING_STATUS_RECEIVED &&
                                                $model->pay_status == OrderInfo::PAY_STATUS_PAYED
                                            ) :
                                        ?>
                                        <button class="btn btn-outline btn-primary btn-xs" xm-action="tracking" data-toggle="modal" data-target="#myModal"  xm-data="<?=$order_goods[$model->order_id]['xmdata']?>">
                                            <?php
                                                if ($model->shipping_status == OrderInfo::SHIPPING_STATUS_SHIPPED) :
                                            ?>
                                                修改发货单号
                                            <?php else : ?>
                                                确认发货
                                            <?php endif; ?>
                                        </button>
                                        <?php endif; ?>

                                        <?php
                                            //  待确认退货订单
                                            if (
                                                $model->order_status == OrderInfo::ORDER_STATUS_RETURNED &&
                                                $model->shipping_status == OrderInfo::SHIPPING_STATUS_RECEIVED &&
                                                $model->pay_status == OrderInfo::PAY_STATUS_PAYED
                                            ) :
                                        ?>
                                        <button class="btn btn-outline btn-primary btn-xs" xm-action="tracking" data-toggle="modal" data-target="#myModal"  xm-data="<?='id='.OrderInfo::getDeliverySn($model->order_id)?>">确认收到退货商品</button>
                                        <?php endif; ?>
                                    </div>
                                </td>

                            </tr>
                        <?php
                            endforeach;
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="9">
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

<div class="footer">
    <div>
        <strong>Copyright</strong> 小美诚品 &copy; <?=date('Y')?>
    </div>
</div>
<!-- 模态框（Modal） -->

<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header text-center">
                <h4 class="modal-title">添加物流信息</h4>
                <small class="font-bold"></small>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>物流名称</label>
                    <input type="text" placeholder="请输入快递/物流公司的名称" class="form-control" id="trackName" name="trackName">
                </div>
                <div class="form-group">
                    <label>物流单号</label>
                    <input type="tel" placeholder="请输入快递/物流的单号" class="form-control" id="trackNo" name="trackNo">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" xm-node="submit">确认添加</button>
            </div>
        </div>
    </div>
</div>

<!-- /.modal -->

<?php
//    if ($active_record_id) {
//        $this->params['get_all_cash'] = '<div class="col-lg-2"><a class="btn btn-w-m btn-outline btn-primary" style="margin-top: 14px;margin-left:20px;position: absolute;" xm-action="getCashAll" href="/index.php?r=brand-divide-record/cash&type=all">所有已完成订单货款提取到钱包</a></div>';
//    }
?>