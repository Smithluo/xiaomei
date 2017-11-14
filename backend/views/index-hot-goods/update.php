<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexHotGoods */

$this->title = '更新热销商品: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '热销商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="index-hot-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
