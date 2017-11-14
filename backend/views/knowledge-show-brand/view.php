<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\KnowledgeShowBrand */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '推荐品牌', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="knowledge-show-brand-view">

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
            'brand_id',
            'sort_order',
            [
                'attribute' => 'sort_order',
                'value' => isset($platformMap[$model->platform]) ? $platformMap[$model->platform] : '未设置',
            ]
        ],
    ]) ?>

</div>

<p>
    <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
</p>
