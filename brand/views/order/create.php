<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OrderInfo */

$this->title = 'Create Order Info';
$this->params['breadcrumbs'][] = ['label' => 'Order Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
