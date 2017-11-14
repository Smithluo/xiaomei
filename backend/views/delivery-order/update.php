<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\DeliveryOrder */

$this->title = 'Update Delivery Order: ' . $model->delivery_id;
$this->params['breadcrumbs'][] = ['label' => 'Delivery Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->delivery_id, 'url' => ['view', 'id' => $model->delivery_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="delivery-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
