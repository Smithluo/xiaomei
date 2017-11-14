<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\IndexZhifaFanpai */

$this->title = 'Create Index Zhifa Fanpai';
$this->params['breadcrumbs'][] = ['label' => 'Index Zhifa Fanpais', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-zhifa-fanpai-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
