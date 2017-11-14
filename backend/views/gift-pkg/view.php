<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\GiftPkg */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '礼包活动', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-pkg-view">

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
            'name',
            [
                'attribute' => 'thumb_img',
                'format' => 'raw',
                'value' => Html::img($model->getUploadUrl('thumb_img'), ['height' => 100])
            ],
            'price',
            [
                'attribute' => 'shipping_code',
                'value' => $shippingList[$model->shipping_code]
            ],
            'brief',
            [
                'attribute' => 'is_on_sale',
                'value' => $isOnSaleMap[$model->is_on_sale]
            ],
            'updated_at',
            'updated_by',
            'pkg_desc:ntext',
        ],
    ]) ?>

</div>
