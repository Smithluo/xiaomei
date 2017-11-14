<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AdPosition */

$this->title = $model->position_id;
$this->params['breadcrumbs'][] = ['label' => 'Ad Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-position-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->position_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->position_id], [
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
            'position_id',
            'position_name',
            'position_desc',
            'position_style:ntext',
        ],
    ]) ?>

</div>
