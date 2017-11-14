<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ActivitySort */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Activity Sorts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-sort-view">

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
            'type',
            'alias',
            'link',
            'sort_order',
            [
                'attribute' => 'is_show',
                'value' => $isShowMap[$model->is_show],
            ],
//            'show_limit', 未启用
        ],
    ]) ?>

</div>
