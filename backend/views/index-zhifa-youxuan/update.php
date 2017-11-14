<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexZhifaYouxuan */

$this->title = 'Update Index Zhifa Youxuan: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Index Zhifa Youxuans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="index-zhifa-youxuan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
