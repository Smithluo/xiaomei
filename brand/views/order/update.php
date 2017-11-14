<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OrderInfo */

$this->title = '订单编号: ' . $model->order_sn;
$this->params['breadcrumbs'][] = ['label' => '订单信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->order_sn, 'url' => ['view', 'id' => $model->order_id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="order-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
