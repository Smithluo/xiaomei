<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryOrder */

$this->title = '发货单: ';
$this->params['breadcrumbs'][] = ['label' => '发货单号', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->delivery_sn, 'url' => ['view', 'id' => $model->delivery_id]];

?>
<div class="delivery-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
