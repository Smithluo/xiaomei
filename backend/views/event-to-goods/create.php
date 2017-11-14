<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EventToGoods */

$this->title = 'Create Event To Goods';
$this->params['breadcrumbs'][] = ['label' => 'Event To Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-to-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
