<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Spu */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Spus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="spu-view">

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
        ],
    ]) ?>

</div>

<div>
    <?php if (!empty($skuGoodsList)) : ?>
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">已关联的商品</div>

            <!-- Table -->
            <table class="table">
                <table class="table">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>商品ID</th>
                        <th>商品名称</th>
                        <th>是否上架</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($skuGoodsList as $goods) :?>
                        <tr>
                            <th scope="row"><?=$goods->goods_id ?></th>
                            <td><?=$goods->goods_name?></td>
                            <td><?=$isOnSaleMap[$goods->is_on_sale]?></td>
                            <td><?=$goods->sku_size?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </table>
        </div>
    <?php else : ?>
        当前SPU未关联商品
    <?php endif; ?>
</div>