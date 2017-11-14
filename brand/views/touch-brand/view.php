<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model brand\models\TouchBrand */

$this->title = $model->brand_id;
$this->params['breadcrumbs'][] = ['label' => 'Touch Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touch-brand-view">

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
            'brand_banner',
            'brand_content:ntext',
            'brand_qualification:ntext',
        ],
    ]) ?>

</div>
