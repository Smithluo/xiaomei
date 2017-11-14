<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model brand\models\BrandDivideRecord */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Brand Divide Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-divide-record-view">

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
            'order_id',
            'brand_id',
            'goods_amount',
            'shipping_fee',
            'user_id',
            'divide_amount',
            'cash_record_id',
            'created_at',
            'status',
        ],
    ]) ?>

</div>
