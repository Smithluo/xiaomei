<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use brand\models\OrderInfo;
use common\models\Region;

/* @var $this yii\web\View */
/* @var $model common\models\OrderInfo */

$this->title = '订单编号：'.$model->order_sn;
$this->params['breadcrumbs'][] = ['label' => '订单详情', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-info-view">

<!--    <h1>--><?php //Html::encode($this->title) ?><!--</h1>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'order_id',
            'order_sn',
//            'user_id',
            [
                'attribute' => 'order_status',
                'value' => OrderInfo::$order_status_map[$model->order_status]
            ],
            [
                'attribute' => 'shipping_status',
                'value' => OrderInfo::$shipping_status_map[$model->shipping_status]
            ],
            [
                'attribute' => 'pay_status',
                'value' => OrderInfo::$pay_status_map[$model->pay_status]
            ],
            'consignee',
//            'country',
//            'province',
            [
                'label' => '区域',
                'format' => 'html',
                'value' => Region::getUserAddress($model)
            ],
//            'city',
//            'district',
            'address',
            'zipcode',
//            'tel',
            'mobile',
//            'email:email',
//            'best_time',
//            'sign_building',
            'postscript',
//            'shipping_id',
//            'shipping_name',
//            'pay_id',
//            'pay_name',
//            'how_oos',
//            'how_surplus',
//            'pack_name',
//            'card_name',
//            'card_message',
//            'inv_payee',
//            'inv_content',
            'goods_amount',
//            'shipping_fee',
//            'insure_fee',
//            'pay_fee',
//            'pack_fee',
//            'card_fee',
//            'money_paid',
//            'surplus',
//            'integral',
//            'integral_money',
//            'bonus',
//            'order_amount',
//            'from_ad',
//            'referer',
//            'add_time',
//            'confirm_time',
//            'pay_time',
            [
                'attribute' => 'shipping_time',
                'value' => date('Y-m-d H:i:s', $model->shipping_time)
            ],
//            'recv_time:datetime',
//            'pack_id',
//            'card_id',
//            'bonus_id',
//            'invoice_no',
//            'extension_code',
//            'extension_id',
//            'to_buyer',
//            'pay_note',
//            'agency_id',
//            'inv_type',
//            'tax',
//            'is_separate',
//            'parent_id',
//            'discount',
//            'mobile_pay',
//            'mobile_order',
        ],

    ]) ?>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->order_id], ['class' => 'btn btn-primary']) ?>
    </p>

</div>
