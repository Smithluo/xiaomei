<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsType */

$this->title = $model->cat_id;
$this->params['breadcrumbs'][] = ['label' => 'Goods Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cat_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cat_id], [
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
            'cat_id',
            'cat_name',
            'enabled',
            'attr_group',
        ],
    ]) ?>

</div>
