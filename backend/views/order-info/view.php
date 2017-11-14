<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderInfo */

$this->title = $model->order_id;
$this->params['breadcrumbs'][] = ['label' => '订单详情', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-info-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->order_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->order_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'order_id',
            'order_sn',
            'user_id',
            'order_status',
            'shipping_status',
            'pay_status',
            'consignee',
            'country',
            'province',
            'city',
            'district',
            'address',
            'zipcode',
            'tel',
            'mobile',
            'email:email',
            'best_time',
            'sign_building',
            'postscript',
            'shipping_id',
            'shipping_name',
            'pay_id',
            'pay_name',
            'how_oos',
            'how_surplus',
            'pack_name',
            'card_name',
            'card_message',
            'inv_payee',
            'inv_content',
            'goods_amount',
            'shipping_fee',
            'insure_fee',
            'pay_fee',
            'pack_fee',
            'card_fee',
            'money_paid',
            'surplus',
            'integral',
            'integral_money',
            'bonus',
            'order_amount',
            'from_ad',
            'referer',
            'add_time',
            'confirm_time',
            'pay_time',
            'shipping_time',
            'recv_time:datetime',
            'pack_id',
            'card_id',
            'bonus_id',
            'invoice_no',
            'extension_code',
            'extension_id',
            'to_buyer',
            'pay_note',
            'agency_id',
            'inv_type',
            'tax',
            'is_separate',
            'parent_id',
            'discount',
            'mobile_pay',
            'mobile_order',
            'brand_id',
        ],
    ]) ?>

</div>
