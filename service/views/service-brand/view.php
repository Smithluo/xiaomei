<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model service\models\Brand */

$this->title = $model->brand_id;
$this->params['breadcrumbs'][] = ['label' => 'Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->brand_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->brand_id], [
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
            'brand_id',
            'brand_name',
            'brand_depot_area',
            'brand_logo',
            'brand_logo_two',
            'brand_bgcolor',
            'brand_policy',
            'brand_desc:ntext',
            'brand_desc_long:ntext',
            'short_brand_desc',
            'site_url:url',
            'sort_order',
            'is_show',
            'album_id',
            'brand_tag',
            'servicer_strategy_id',
        ],
    ]) ?>

</div>
