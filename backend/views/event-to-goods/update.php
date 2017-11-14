<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EventToGoods */

$this->title = 'Update Event To Goods: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Event To Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-to-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
