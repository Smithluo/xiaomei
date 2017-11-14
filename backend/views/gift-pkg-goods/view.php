<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\GiftPkgGoods */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '礼包商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-pkg-goods-view">

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
            'gift_pkg_id',
            'goods_id',
            'goods_num',
        ],
    ]) ?>

</div>
