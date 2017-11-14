<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexFullCutGoods */

$this->title = '更新首页显示的满减商品: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '首页显示的满减商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="index-full-gift-goods-update">

    <?= $this->render('_form', [
        'model' => $model,
        'goodsName' => $goodsName,
    ]) ?>
</div>
