<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsAction */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Goods Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-action-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_name',
            'goods_id',
            'goods_name',
            'shop_price',
            'disable_discount',
            'volume_price:ntext',
            'time',
        ],
    ]) ?>

</div>
