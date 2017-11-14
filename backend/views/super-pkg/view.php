<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SuperPkg */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '超值礼包', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="super-pkg-view">

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
            'pag_name',
            'pag_desc',
            [
                'attribute' => 'gift_pkg_id',
                'value' => $model->giftPkg->name,
            ],
            'sort_order',
            [
                'attribute' => 'start_time',
                'value' => $model->start_time,

            ],

            [
                'attribute' => 'end_time',
                'value' => $model->end_time,
            ],
        ],
    ]) ?>

</div>
