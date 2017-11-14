<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\DeliveryOrder */

$this->title = $model->delivery_id;
$this->params['breadcrumbs'][] = ['label' => 'Delivery Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->delivery_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->delivery_id], [
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
            'delivery_id',
            'delivery_sn',
            'order_sn',
            'order_id',
            'invoice_no',
            'add_time',
            'shipping_id',
            'shipping_name',
            'user_id',
            'action_user',
            'consignee',
            'address',
            'country',
            'province',
            'city',
            'district',
            'sign_building',
            'email:email',
            'zipcode',
            'tel',
            'mobile',
            'best_time',
            'postscript',
            'how_oos',
            'insure_fee',
            'shipping_fee',
            'update_time',
            'suppliers_id',
            'status',
            'agency_id',
        ],
    ]) ?>

</div>
