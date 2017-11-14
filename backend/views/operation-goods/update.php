<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */

$this->title = '编辑商品: ' . $model->goods_name;
$this->params['breadcrumbs'][] = ['label' => 'Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->goods_name, 'url' => ['view', 'id' => $model->goods_id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'allTagIds' => $allTagIds,
    ]) ?>

</div>
