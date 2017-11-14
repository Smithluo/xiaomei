<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsSupplyInfo */

$this->title = 'Update Goods Supply Info: ' . $model->goods_id;
$this->params['breadcrumbs'][] = ['label' => 'Goods Supply Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->goods_id, 'url' => ['view', 'id' => $model->goods_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="goods-supply-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
