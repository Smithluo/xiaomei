<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model home\models\BrandApplication */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Brand Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-application-view">

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
            'company_name',
            'company_address',
            'name',
            'position',
            'contact',
            'brands',
            'licence',
            'recorded',
            'registed',
            'taxed',
            'checked',
        ],
    ]) ?>

</div>
