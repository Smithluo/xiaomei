<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexPaihangGoods */

$this->title = '更新排行商品: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '排行商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="index-paihang-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
