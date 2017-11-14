<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\IndexPaihangGoods */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Index Paihang Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-paihang-goods-view">

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
            'title',
            'floor_id',
            'description:ntext',
            'goods_id',
            'sort_order',
        ],
    ]) ?>

</div>
