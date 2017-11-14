<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\NewArrivedGoods */

$this->title = 'Create New Arrived Goods';
$this->params['breadcrumbs'][] = ['label' => 'New Arrived Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="new-arrived-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
