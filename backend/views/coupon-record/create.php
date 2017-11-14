<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\CouponRecord */

$this->title = '创建优惠券';
$this->params['breadcrumbs'][] = ['label' => '优惠券流水', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'usersMap' => $usersMap,
        'couponStatusMap' => $couponStatusMap,

    ]) ?>

</div>
