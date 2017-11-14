<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DeliveryOrder */

$this->title = 'Create Delivery Order';
$this->params['breadcrumbs'][] = ['label' => 'Delivery Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
