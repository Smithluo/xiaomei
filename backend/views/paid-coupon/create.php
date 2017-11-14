<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PaidCoupon */

$this->title = 'Create Paid Coupon';
$this->params['breadcrumbs'][] = ['label' => 'Paid Coupons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paid-coupon-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
