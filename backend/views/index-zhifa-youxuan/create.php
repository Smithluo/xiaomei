<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\IndexZhifaYouxuan */

$this->title = 'Create Index Zhifa Youxuan';
$this->params['breadcrumbs'][] = ['label' => 'Index Zhifa Youxuans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-zhifa-youxuan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
