<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CouponTopicGoods */

$this->title = 'Update Coupon Topic Goods: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Topic Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coupon-topic-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
