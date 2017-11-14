<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CouponTopicGoods */

$this->title = 'Create Coupon Topic Goods';
$this->params['breadcrumbs'][] = ['label' => 'Coupon Topic Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-topic-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
