<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderInfo */

$this->title = '订单详情: ' . $model->order_sn;
$this->params['breadcrumbs'][] = ['label' => '订单信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->order_id, 'url' => ['view', 'id' => $model->order_id]];
?>
<div class="order-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'actions' => $actions,
        'isGiftStyleMap' => $isGiftStyleMap,
    ]) ?>

</div>
