<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Brand */

$this->title = 'Update Brand: ' . $model->brand_id;
$this->params['breadcrumbs'][] = ['label' => 'Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->brand_id, 'url' => ['view', 'id' => $model->brand_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="brand-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
