<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\BrandPolicy */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '品牌增值政策', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-policy-view">

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
            [
                'attribute' => 'brand_id',
                'value' => function ($model) {
                    return $model->brand->brand_name;
                }
            ],
            'policy_content',
            [
                'attribute' => 'policy_link',
                'format' => 'url',
            ],
            'sort_order',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return \common\models\BrandPolicy::$statusMap[$model->status];
                }
            ],
        ],
    ]) ?>

</div>
