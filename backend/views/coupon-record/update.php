<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CouponRecord */

$this->title = '修改优惠券: ' . $model->coupon_id;
$this->params['breadcrumbs'][] = ['label' => '优惠券列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->coupon_id, 'url' => ['view', 'id' => $model->coupon_id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="coupon-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'usersMap' => $usersMap,
        'couponStatusMap' => $couponStatusMap,
    ]) ?>

</div>
