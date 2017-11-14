<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\IndexStarBrandConf */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '首页楼层品牌配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-star-brand-conf-view">

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
            'tab_id',
            'sort_order',
        ],
    ]) ?>

</div>
