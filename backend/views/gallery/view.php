<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Gallery */

$this->title = $model->gallery_id;
$this->params['breadcrumbs'][] = ['label' => '相册', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gallery-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->gallery_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->gallery_id], [
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
            'gallery_id',
            'gallery_name',
            'sort_order',
            [
                'attribute' => 'is_show',
                'value' => $isShowMap[$model->is_show],
            ],
        ],
    ]) ?>

</div>

<p>
    <?= Html::a('新建相册', ['create'], ['class' => 'btn btn-success']) ?>
</p>